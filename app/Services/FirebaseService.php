<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

/**
 * Firebase Service - Main Firebase connection (REST API - No gRPC required)
 */
class FirebaseService
{
    protected $database;
    protected $projectId;
    protected $credentials;
    
    public function __construct()
    {
        $factory = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'))
            ->withDatabaseUri(config('firebase.database.url'));
        
        $this->database = $factory->createDatabase();
        
        // For Firestore REST API
        $credentialsFile = config('firebase.credentials.file');
        $this->credentials = json_decode(file_get_contents($credentialsFile), true);
        $this->projectId = $this->credentials['project_id'];
    }
    
    public function getDatabase()
    {
        return $this->database;
    }
    
    /**
     * Get Firestore document using REST API
     */
    public function getDocument($collection, $documentId)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$documentId}";
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ])->get($url);
        
        if ($response->successful()) {
            return $this->parseFirestoreDocument($response->json());
        }
        
        return null;
    }
    
    /**
     * Query Firestore collection using REST API
     */
    public function queryCollection($collection, $filters = [], $orderBy = null, $limit = null)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents:runQuery";
        
        $query = [
            'structuredQuery' => [
                'from' => [['collectionId' => $collection]]
            ]
        ];
        
        // Add filters
        if (!empty($filters)) {
            $query['structuredQuery']['where'] = $this->buildFilters($filters);
        }
        
        // Add order by
        if ($orderBy) {
            $query['structuredQuery']['orderBy'] = [$orderBy];
        }
        
        // Add limit
        if ($limit) {
            $query['structuredQuery']['limit'] = $limit;
        }
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ])->post($url, $query);
        
        if ($response->successful()) {
            return $this->parseQueryResults($response->json());
        }
        
        return [];
    }
    
    /**
     * Add document to collection
     */
    public function addDocument($collection, $data)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}";
        
        $document = $this->buildFirestoreDocument($data);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ])->post($url, ['fields' => $document]);
        
        return $response->successful();
    }
    
    /**
     * Update document
     */
    public function updateDocument($collection, $documentId, $data)
    {
        $url = "https://firestore.googleapis.com/v1/projects/{$this->projectId}/databases/(default)/documents/{$collection}/{$documentId}";
        
        $document = $this->buildFirestoreDocument($data);
        
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $this->getAccessToken(),
        ])->patch($url, ['fields' => $document]);
        
        return $response->successful();
    }
    
    /**
     * Get access token for API authentication
     */
    private function getAccessToken()
    {
        // Cache token for 1 hour
        return cache()->remember('firebase_access_token', 3600, function() {
            $jwt = $this->createJWT();
            
            $response = Http::asForm()->post('https://oauth2.googleapis.com/token', [
                'grant_type' => 'urn:ietf:params:oauth:grant-type:jwt-bearer',
                'assertion' => $jwt
            ]);
            
            if ($response->successful()) {
                return $response->json()['access_token'];
            }
            
            throw new \Exception('Failed to get Firebase access token');
        });
    }
    
    /**
     * Create JWT for authentication
     */
    private function createJWT()
    {
        $now = time();
        $payload = [
            'iss' => $this->credentials['client_email'],
            'scope' => 'https://www.googleapis.com/auth/datastore',
            'aud' => 'https://oauth2.googleapis.com/token',
            'exp' => $now + 3600,
            'iat' => $now
        ];
        
        $header = base64_encode(json_encode(['alg' => 'RS256', 'typ' => 'JWT']));
        $payload = base64_encode(json_encode($payload));
        
        $signatureInput = $header . '.' . $payload;
        
        openssl_sign($signatureInput, $signature, $this->credentials['private_key'], OPENSSL_ALGO_SHA256);
        $signature = base64_encode($signature);
        
        return $header . '.' . $payload . '.' . $signature;
    }
    
    /**
     * Build Firestore filters
     */
    private function buildFilters($filters)
    {
        $compositeFilters = [];
        
        foreach ($filters as $filter) {
            $compositeFilters[] = [
                'fieldFilter' => [
                    'field' => ['fieldPath' => $filter['field']],
                    'op' => $filter['op'],
                    'value' => $this->convertValue($filter['value'])
                ]
            ];
        }
        
        if (count($compositeFilters) === 1) {
            return $compositeFilters[0];
        }
        
        return [
            'compositeFilter' => [
                'op' => 'AND',
                'filters' => $compositeFilters
            ]
        ];
    }
    
    /**
     * Convert PHP value to Firestore format
     */
    private function convertValue($value)
    {
        if (is_string($value)) {
            return ['stringValue' => $value];
        } elseif (is_int($value)) {
            return ['integerValue' => (string)$value];
        } elseif (is_float($value)) {
            return ['doubleValue' => $value];
        } elseif (is_bool($value)) {
            return ['booleanValue' => $value];
        } elseif ($value instanceof \DateTime) {
            return ['timestampValue' => $value->format('Y-m-d\TH:i:s\Z')];
        }
        
        return ['nullValue' => null];
    }
    
    /**
     * Build Firestore document structure
     */
    private function buildFirestoreDocument($data)
    {
        $fields = [];
        
        foreach ($data as $key => $value) {
            $fields[$key] = $this->convertValue($value);
        }
        
        return $fields;
    }
    
    /**
     * Parse Firestore document response
     */
    private function parseFirestoreDocument($document)
    {
        if (!isset($document['fields'])) {
            return null;
        }
        
        $result = [];
        foreach ($document['fields'] as $key => $value) {
            $result[$key] = $this->parseValue($value);
        }
        
        // Add document ID
        if (isset($document['name'])) {
            $parts = explode('/', $document['name']);
            $result['id'] = end($parts);
        }
        
        return $result;
    }
    
    /**
     * Parse query results
     */
    private function parseQueryResults($results)
    {
        $documents = [];
        
        foreach ($results as $result) {
            if (isset($result['document'])) {
                $documents[] = $this->parseFirestoreDocument($result['document']);
            }
        }
        
        return $documents;
    }
    
    /**
     * Parse Firestore value
     */
    private function parseValue($value)
    {
        if (isset($value['stringValue'])) {
            return $value['stringValue'];
        } elseif (isset($value['integerValue'])) {
            return (int)$value['integerValue'];
        } elseif (isset($value['doubleValue'])) {
            return (float)$value['doubleValue'];
        } elseif (isset($value['booleanValue'])) {
            return $value['booleanValue'];
        } elseif (isset($value['timestampValue'])) {
            return new \DateTime($value['timestampValue']);
        } elseif (isset($value['arrayValue'])) {
            $array = [];
            if (isset($value['arrayValue']['values'])) {
                foreach ($value['arrayValue']['values'] as $item) {
                    $array[] = $this->parseValue($item);
                }
            }
            return $array;
        }
        
        return null;
    }
    
    /**
     * Get user's calorie goal (using REST API)
     */
    public function getUserCalorieGoal($userId)
    {
        $user = $this->getDocument('users', $userId);
        return $user['calorieGoal'] ?? 2000;
    }
    
    /**
     * Update user's calorie goal
     */
    public function updateUserCalorieGoal($userId, $calorieGoal)
    {
        return $this->updateDocument('users', $userId, ['calorieGoal' => $calorieGoal]);
    }
}

/**
 * Calorie Log Service - Manages food logs
 */
class CalorieLogService
{
    protected $firebaseService;
    
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    
    /**
     * Get user data
     */
    public function getUserData($userId)
    {
        return $this->firebaseService->getDocument('users', $userId);
    }
    
    /**
     * Get today's logs
     */
    public function getTodayLogs($userId)
    {
        $today = Carbon::today();
        $tomorrow = Carbon::tomorrow();
        
        return $this->firebaseService->queryCollection('calorieLogs', [
            ['field' => 'userId', 'op' => 'EQUAL', 'value' => $userId],
            ['field' => 'timestamp', 'op' => 'GREATER_THAN_OR_EQUAL', 'value' => $today->toDateTime()],
            ['field' => 'timestamp', 'op' => 'LESS_THAN', 'value' => $tomorrow->toDateTime()]
        ]);
    }
    
    /**
     * Get logs for date range
     */
    public function getLogsForDateRange($userId, $startDate, $endDate)
    {
        return $this->firebaseService->queryCollection('calorieLogs', [
            ['field' => 'userId', 'op' => 'EQUAL', 'value' => $userId],
            ['field' => 'timestamp', 'op' => 'GREATER_THAN_OR_EQUAL', 'value' => $startDate->toDateTime()],
            ['field' => 'timestamp', 'op' => 'LESS_THAN_OR_EQUAL', 'value' => $endDate->toDateTime()]
        ]);
    }
    
    /**
     * Calculate total calories from logs
     */
    public function calculateTotalCalories($logs)
    {
        $total = 0;
        foreach ($logs as $log) {
            $total += $log['calories'] ?? 0;
        }
        return $total;
    }
    
    /**
     * Add a calorie log
     */
    public function addLog($userId, $data)
    {
        $logData = array_merge($data, [
            'userId' => $userId,
            'timestamp' => now()->toDateTime()
        ]);
        
        return $this->firebaseService->addDocument('calorieLogs', $logData);
    }
}

/**
 * Recipe Service - Manages user recipes
 */
class RecipeService
{
    protected $firebaseService;
    
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    
    /**
     * Get public recipes
     */
    public function getPublicRecipes($limit = 10)
    {
        return $this->firebaseService->queryCollection('recipes', 
            [['field' => 'isPublic', 'op' => 'EQUAL', 'value' => true]],
            ['field' => ['fieldPath' => 'createdAt'], 'direction' => 'DESCENDING'],
            $limit
        );
    }
    
    /**
     * Get recipe by ID
     */
    public function getRecipeById($recipeId)
    {
        return $this->firebaseService->getDocument('recipes', $recipeId);
    }
    
    /**
     * Get user's recipes
     */
    public function getUserRecipes($userId, $limit = 10)
    {
        return $this->firebaseService->queryCollection('recipes', 
            [['field' => 'userId', 'op' => 'EQUAL', 'value' => $userId]],
            ['field' => ['fieldPath' => 'createdAt'], 'direction' => 'DESCENDING'],
            $limit
        );
    }
}

/**
 * Cooked Recipes Service - Tracks what users have cooked
 */
class CookedRecipesService
{
    protected $firebaseService;
    
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }
    
    /**
     * Get user's cooked recipes
     */
    public function getUserCookedRecipes($userId, $limit = 10)
    {
        return $this->firebaseService->queryCollection('cookedRecipes',
            [['field' => 'userId', 'op' => 'EQUAL', 'value' => $userId]],
            ['field' => ['fieldPath' => 'cookedAt'], 'direction' => 'DESCENDING'],
            $limit
        );
    }
    
    /**
     * Get most cooked recipes
     */
    public function getMostCookedRecipes($userId, $limit = 5)
    {
        $cooked = $this->firebaseService->queryCollection('cookedRecipes',
            [['field' => 'userId', 'op' => 'EQUAL', 'value' => $userId]]
        );
        
        // Count frequency
        $frequency = [];
        foreach ($cooked as $doc) {
            $recipeId = $doc['recipeId'];
            
            if (!isset($frequency[$recipeId])) {
                $frequency[$recipeId] = [
                    'recipeId' => $recipeId,
                    'count' => 0
                ];
            }
            $frequency[$recipeId]['count']++;
        }
        
        // Sort by count
        usort($frequency, function($a, $b) {
            return $b['count'] - $a['count'];
        });
        
        return array_slice($frequency, 0, $limit);
    }
    
    /**
     * Mark recipe as cooked
     */
    public function markAsCooked($userId, $recipeId)
    {
        return $this->firebaseService->addDocument('cookedRecipes', [
            'userId' => $userId,
            'recipeId' => $recipeId,
            'cookedAt' => now()->toDateTime()
        ]);
    }
}

/**
 * TheMealDB Service - External API integration
 */
class TheMealDBService
{
    protected $baseUrl = 'https://www.themealdb.com/api/json/v1/1/';
    
    /**
     * Get random meals
     */
    public function getRandomMeals($count = 5)
    {
        $meals = [];
        
        for ($i = 0; $i < $count; $i++) {
            $response = Http::get($this->baseUrl . 'random.php');
            
            if ($response->successful()) {
                $data = $response->json();
                if (isset($data['meals'][0])) {
                    $meals[] = $this->formatMeal($data['meals'][0]);
                }
            }
        }
        
        return $meals;
    }
    
    /**
     * Get meal details by ID
     */
    public function getMealDetails($mealId)
    {
        $response = Http::get($this->baseUrl . "lookup.php?i={$mealId}");
        
        if ($response->successful()) {
            $data = $response->json();
            if (isset($data['meals'][0])) {
                return $this->formatMeal($data['meals'][0]);
            }
        }
        
        return null;
    }
    
    /**
     * Get meals by category
     */
    public function getMealsByCategory($category, $number = 10)
    {
        $response = Http::get($this->baseUrl . "filter.php?c={$category}");
        
        if ($response->successful()) {
            $data = $response->json();
            
            if (isset($data['meals']) && is_array($data['meals'])) {
                $meals = array_slice($data['meals'], 0, $number);
                
                $formatted = [];
                foreach ($meals as $meal) {
                    $formatted[] = [
                        'id' => $meal['idMeal'],
                        'title' => $meal['strMeal'],
                        'image' => $meal['strMealThumb'],
                        'category' => $category
                    ];
                }
                
                return $formatted;
            }
        }
        
        return [];
    }
    
    /**
     * Format meal data to match app structure
     */
    private function formatMeal($meal)
    {
        return [
            'id' => $meal['idMeal'],
            'title' => $meal['strMeal'],
            'name' => $meal['strMeal'],
            'image' => $meal['strMealThumb'],
            'author' => 'TheMealDB',
            'category' => $meal['strCategory'] ?? '',
            'area' => $meal['strArea'] ?? '',
            'instructions' => $meal['strInstructions'] ?? '',
            'readyInMinutes' => 0,
            'calories' => 0,
            'averageRating' => 0,
            'totalRatings' => 0
        ];
    }
}