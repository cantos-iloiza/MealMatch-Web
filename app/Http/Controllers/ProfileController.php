<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;

class ProfileController extends Controller
{
    protected $firebase;
    protected $firestore;

    public function __construct()
    {
        try {
            $factory = (new Factory)->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS_PATH')));
            $this->firebase = $factory->createAuth();
            $this->firestore = $factory->createFirestore()->database();
        } catch (\Exception $e) {
            Log::error('Firebase initialization error: ' . $e->getMessage());
        }
    }

    /**
     * Display the user profile page
     */
    public function index(Request $request)
    {
        // Check if user is authenticated
        $uid = session('firebase_uid');
        
        if (!$uid) {
            return redirect()->route('login')->with('error', 'Please log in to view your profile');
        }

        return view('profile');
    }
    
    /**
     * Get logs within a date range
     * Matches Flutter: LogService.getLogsInRange(start, end)
     */
    public function getLogsInRange(Request $request)
    {
        try {
            $startDate = $request->input('start_date'); // YYYY-MM-DD
            $endDate = $request->input('end_date'); // YYYY-MM-DD
            
            if (!$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'start_date and end_date parameters are required'
                ], 400);
            }
            
            // Validate date format
            $start = \DateTime::createFromFormat('Y-m-d', $startDate);
            $end = \DateTime::createFromFormat('Y-m-d', $endDate);
            
            if (!$start || !$end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Use YYYY-MM-DD'
                ], 400);
            }
            
            if ($start > $end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Start date must be before or equal to end date'
                ], 400);
            }

            // Get authenticated user ID
            $userId = session('firebase_uid');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if Firebase/Firestore is initialized
            if (!$this->firestore) {
                throw new \Exception('Firebase connection not available');
            }
            
            // Query meal_logs between dates from Firestore
            $mealLogsRef = $this->firestore->collection('users')->document($userId)->collection('meal_logs');
            $query = $mealLogsRef
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->orderBy('date')
                ->orderBy('timestamp');
            
            $documents = $query->documents();
            
            // Group logs by date and category
            $filtered = [];
            
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $data = $doc->data();
                    $date = $data['date'] ?? null;
                    $category = $data['category'] ?? 'Snacks';
                    
                    if (!$date) continue;
                    
                    if (!isset($filtered[$date])) {
                        $filtered[$date] = [
                            'Breakfast' => [],
                            'Lunch' => [],
                            'Dinner' => [],
                            'Snacks' => []
                        ];
                    }
                    
                    $log = [
                        'id' => $doc->id(),
                        'foodName' => $data['food_name'] ?? 'Unknown Food',
                        'calories' => $data['calories'] ?? 0,
                        'carbs' => $data['carbs'] ?? 0,
                        'proteins' => $data['proteins'] ?? 0,
                        'fats' => $data['fats'] ?? 0,
                        'serving' => $data['serving'] ?? '',
                        'brand' => $data['brand'] ?? '',
                        'category' => $category,
                        'timestamp' => $data['timestamp'] ?? null,
                        'isVerified' => $data['is_verified'] ?? false,
                        'source' => $data['source'] ?? ''
                    ];
                    
                    if (isset($filtered[$date][$category])) {
                        $filtered[$date][$category][] = $log;
                    }
                }
            }
            
            // Fill in missing dates with empty arrays
            $current = clone $start;
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                if (!isset($filtered[$dateStr])) {
                    $filtered[$dateStr] = [
                        'Breakfast' => [],
                        'Lunch' => [],
                        'Dinner' => [],
                        'Snacks' => []
                    ];
                }
                $current->modify('+1 day');
            }
            
            // Sort by date
            ksort($filtered);
            
            return response()->json([
                'success' => true,
                'data' => $filtered
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching logs in range: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve logs. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get logs grouped by category for a specific date
     * Matches Flutter: LogService.getLogsGroupedByCategory(date)
     */
    public function getLogsGroupedByCategory(Request $request)
    {
        try {
            $date = $request->input('date'); // YYYY-MM-DD format
            
            if (!$date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date parameter is required'
                ], 400);
            }

            // Get authenticated user ID
            $userId = session('firebase_uid');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if Firebase/Firestore is initialized
            if (!$this->firestore) {
                throw new \Exception('Firebase connection not available');
            }
            
            // Query meal_logs for specific date from Firestore
            $mealLogsRef = $this->firestore->collection('users')->document($userId)->collection('meal_logs');
            $query = $mealLogsRef->where('date', '==', $date)->orderBy('timestamp');
            
            $documents = $query->documents();
            
            $grouped = [
                'Breakfast' => [],
                'Lunch' => [],
                'Dinner' => [],
                'Snacks' => []
            ];
            
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $data = $doc->data();
                    $log = [
                        'id' => $doc->id(),
                        'foodName' => $data['food_name'] ?? 'Unknown Food',
                        'calories' => $data['calories'] ?? 0,
                        'carbs' => $data['carbs'] ?? 0,
                        'proteins' => $data['proteins'] ?? 0,
                        'fats' => $data['fats'] ?? 0,
                        'serving' => $data['serving'] ?? '',
                        'brand' => $data['brand'] ?? '',
                        'category' => $data['category'] ?? 'Snacks',
                        'timestamp' => $data['timestamp'] ?? null,
                        'isVerified' => $data['is_verified'] ?? false,
                        'source' => $data['source'] ?? ''
                    ];
                    
                    $category = $log['category'];
                    if (isset($grouped[$category])) {
                        $grouped[$category][] = $log;
                    }
                }
            }
            
            // Sort each category by timestamp (newest first)
            foreach ($grouped as $category => $logs) {
                usort($grouped[$category], function($a, $b) {
                    return $b['timestamp'] <=> $a['timestamp'];
                });
            }
            
            return response()->json([
                'success' => true,
                'data' => $grouped
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching logs by category: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve logs. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get user profile data from Firebase
     * Matches Flutter: FirebaseService.getUserData()
     */
    public function getUserData(Request $request)
    {
        try {
            // Get authenticated user ID
            $userId = session('firebase_uid');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if Firebase/Firestore is initialized
            if (!$this->firestore) {
                throw new \Exception('Firebase connection not available');
            }

            // Get user data from Firestore
            $userDoc = $this->firestore->collection('users')->document($userId)->snapshot();
            
            if (!$userDoc->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'User profile not found'
                ], 404);
            }

            $userData = $userDoc->data();
            
            // Try to get email from Firestore first, then from Firebase Auth
            $email = $userData['email'] ?? '';
            if (empty($email)) {
                try {
                    $authUser = $this->firebase->getUser($userId);
                    $email = $authUser->email ?? '';
                } catch (\Exception $e) {
                    Log::warning('Could not fetch email from Firebase Auth: ' . $e->getMessage());
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => $userData['display_name'] ?? 'User',
                    'email' => $email,
                    'photoURL' => $userData['avatar'] ?? null,
                    'goalCalories' => $userData['calorieGoal'] ?? 2000
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching user data: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve user profile. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get weekly streak data from Firebase
     */
    public function getWeeklyStreak(Request $request)
    {
        try {
            // Get authenticated user ID
            $userId = session('firebase_uid');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if Firebase/Firestore is initialized
            if (!$this->firestore) {
                throw new \Exception('Firebase connection not available');
            }

            // Calculate weekly streak
            $today = new \DateTime();
            $weekStart = (clone $today)->modify('last sunday');
            
            $weekDates = [];
            for ($i = 0; $i < 7; $i++) {
                $date = (clone $weekStart)->modify("+$i days");
                $weekDates[] = $date->format('Y-m-d');
            }

            $mealLogsRef = $this->firestore->collection('users')->document($userId)->collection('meal_logs');
            
            $streakDays = 0;
            foreach ($weekDates as $date) {
                $query = $mealLogsRef->where('date', '==', $date)->limit(1);
                $docs = $query->documents();
                
                if (!$docs->isEmpty()) {
                    $streakDays++;
                }
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'weeklyStreak' => $streakDays
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching weekly streak: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve streak data. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get highest streak from user stats
     */
    public function getHighestStreak(Request $request)
    {
        try {
            // Get authenticated user ID
            $userId = session('firebase_uid');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if Firebase/Firestore is initialized
            if (!$this->firestore) {
                throw new \Exception('Firebase connection not available');
            }

            // Get user stats from Firestore
            $userDoc = $this->firestore->collection('users')->document($userId)->snapshot();
            
            if (!$userDoc->exists()) {
                return response()->json([
                    'success' => true,
                    'data' => ['highestStreak' => 0]
                ]);
            }

            $userData = $userDoc->data();
            $highestStreak = $userData['highestStreak'] ?? 0;
            
            return response()->json([
                'success' => true,
                'data' => [
                    'highestStreak' => $highestStreak
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching highest streak: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve streak data. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get average daily calorie intake (last 30 days)
     */
    public function getAverageCalories(Request $request)
    {
        try {
            // Get authenticated user ID
            $userId = session('firebase_uid');
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }

            // Check if Firebase/Firestore is initialized
            if (!$this->firestore) {
                throw new \Exception('Firebase connection not available');
            }

            // Calculate average calories for last 30 days
            $today = new \DateTime();
            $thirtyDaysAgo = (clone $today)->modify('-30 days');
            
            $startDate = $thirtyDaysAgo->format('Y-m-d');
            $endDate = $today->format('Y-m-d');

            $mealLogsRef = $this->firestore->collection('users')->document($userId)->collection('meal_logs');
            $query = $mealLogsRef
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate);
            
            $documents = $query->documents();
            
            $dailyTotals = [];
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $data = $doc->data();
                    $date = $data['date'] ?? null;
                    $calories = $data['calories'] ?? 0;
                    
                    if ($date) {
                        if (!isset($dailyTotals[$date])) {
                            $dailyTotals[$date] = 0;
                        }
                        $dailyTotals[$date] += $calories;
                    }
                }
            }
            
            $averageCalories = 0;
            if (count($dailyTotals) > 0) {
                $totalCalories = array_sum($dailyTotals);
                $averageCalories = round($totalCalories / count($dailyTotals));
            }
            
            return response()->json([
                'success' => true,
                'data' => [
                    'averageCalories' => $averageCalories
                ]
            ]);
            
        } catch (\Exception $e) {
            Log::error('Error fetching average calories: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Unable to retrieve calorie data. Please try again later.',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
