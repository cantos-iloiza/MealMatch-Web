<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\MealLog;
use App\Models\Recipe;
use App\Models\Favorite;
use App\Services\TheMealDBService;
use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Kreait\Firebase\Factory;

class HomeController extends Controller
{
    protected $mealDBService;
    protected $firebase;

    public function __construct(TheMealDBService $mealDBService)
    {
        $this->mealDBService = $mealDBService;
        
        // Initialize Firebase Admin SDK
        $this->firebase = (new Factory())
            ->withServiceAccount(storage_path(env('FIREBASE_CREDENTIALS_PATH')))
            ->createAuth();
    }

    /**
     * Display the home page
     */
    public function index(Request $request)
    {
        $user = $this->getCurrentUser($request);
        
        if (!$user) {
            // Guest user - show default values
            $userGoalCalories = 2000;
            $consumedCalories = 0;
            $userBMR = null;
            $userTDEE = null;
            $hasLoggedMeals = false;
            $hasCookedRecipes = false;
            $showWelcomeDialog = false;
            
            return view('home', compact(
                'user',
                'userGoalCalories',
                'consumedCalories',
                'userBMR',
                'userTDEE',
                'hasLoggedMeals',
                'hasCookedRecipes',
                'showWelcomeDialog'
            ));
        }
        
        // Load critical data first (Phase 1 - Fast)
        $userGoalCalories = $user->calorie_goal ?? 2000;
        
        $consumedCalories = MealLog::where('firebase_uid', $user->firebase_uid)
            ->whereDate('date', today())
            ->sum('calories');
        
        // Calculate BMR and TDEE
        $userBMR = $this->calculateBMR(
            $user->gender,
            $user->age,
            $user->height,
            $user->weight
        );
        $userTDEE = $userBMR * $this->getActivityMultiplier($user->activity_level);
        
        // Check user history
        $hasLoggedMeals = $this->checkUserHasMealHistory($user->firebase_uid);
        $hasCookedRecipes = $this->checkUserHasCookedRecipes($user->firebase_uid);
        
        // Check if should show welcome dialog
        $showWelcomeDialog = session()->get('show_calorie_welcome', false);
        if ($showWelcomeDialog) {
            session()->forget('show_calorie_welcome');
        }
        
        return view('home', compact(
            'user',
            'userGoalCalories',
            'consumedCalories',
            'userBMR',
            'userTDEE',
            'hasLoggedMeals',
            'hasCookedRecipes',
            'showWelcomeDialog'
        ));
    }
    
    /**
     * Load recipes via AJAX (Phase 2 - Loaded after page renders)
     */
    public function loadRecipes(Request $request)
    {
        $user = $this->getCurrentUser($request);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }
        
        // Check user history for "Cook Again" section
        $hasLoggedMeals = $this->checkUserHasMealHistory($user->firebase_uid);
        $hasCookedRecipes = $this->checkUserHasCookedRecipes($user->firebase_uid);
        
        // Load all recipes in parallel
        $cookAgainRecipes = $this->loadCookAgainRecipes($user->firebase_uid, $hasCookedRecipes, $hasLoggedMeals);
        $communityRecipes = Recipe::where('firebase_uid', '!=', $user->firebase_uid)
            ->latest()
            ->limit(5)
            ->get();
        $tryTheseRecipes = $this->mealDBService->getRandomMeals(5);
        $proteinRecipes = $this->getVariedProteinRecipes();
        
        return response()->json([
            'success' => true,
            'cookAgainRecipes' => $cookAgainRecipes,
            'communityRecipes' => $communityRecipes,
            'tryTheseRecipes' => $tryTheseRecipes,
            'proteinRecipes' => $proteinRecipes,
            'hasCookedRecipes' => $hasCookedRecipes,
            'hasLoggedMeals' => $hasLoggedMeals
        ]);
    }
    
    /**
     * Refresh today's calorie data via AJAX
     */
    public function refreshCalories(Request $request)
    {
        $user = $this->getCurrentUser($request);
        
        if (!$user) {
            return response()->json([
                'success' => false,
                'message' => 'Not authenticated'
            ], 401);
        }
        
        $consumedCalories = MealLog::where('firebase_uid', $user->firebase_uid)
            ->whereDate('date', today())
            ->sum('calories');
        
        return response()->json([
            'success' => true,
            'consumedCalories' => $consumedCalories
        ]);
    }
    
    /**
     * Check if user has meal history
     */
    private function checkUserHasMealHistory($firebaseUid)
    {
        return MealLog::where('firebase_uid', $firebaseUid)
            ->where('date', '>=', Carbon::now()->subDays(30))
            ->exists();
    }
    
    /**
     * Check if user has cooked recipes
     */
    private function checkUserHasCookedRecipes($firebaseUid)
    {
        return Recipe::where('firebase_uid', $firebaseUid)
            ->exists();
    }
    
    /**
     * Load "Cook Again" recipes based on user history
     */
    private function loadCookAgainRecipes($firebaseUid, $hasCookedRecipes, $hasLoggedMeals)
    {
        if ($hasCookedRecipes) {
            return $this->getMostCookedRecipes($firebaseUid);
        } elseif ($hasLoggedMeals) {
            return $this->getUserFavoriteRecipes($firebaseUid);
        }
        
        return [];
    }
    
    /**
     * Get user's most cooked recipes (from their custom recipes)
     */
    private function getMostCookedRecipes($firebaseUid)
    {
        return Recipe::where('firebase_uid', $firebaseUid)
            ->latest()
            ->limit(5)
            ->get()
            ->map(function($recipe) {
                // Calculate calories if not set
                if (!$recipe->calories && $recipe->nutrients) {
                    $recipe->calories = $recipe->calculated_calories;
                }
                return $recipe;
            });
    }
    
    /**
     * Get user's favorite recipes from meal logs
     */
    private function getUserFavoriteRecipes($firebaseUid)
    {
        // Get most frequently logged recipes
        $frequentRecipes = MealLog::where('firebase_uid', $firebaseUid)
            ->whereNotNull('recipe_id')
            ->where('date', '>=', Carbon::now()->subDays(60))
            ->select('recipe_id', 'food_name')
            ->selectRaw('COUNT(*) as frequency')
            ->groupBy('recipe_id', 'food_name')
            ->orderByDesc('frequency')
            ->limit(5)
            ->get();
        
        $recipes = [];
        foreach ($frequentRecipes as $index => $log) {
            // Add timeout protection - stop after 3 recipes or 5 seconds
            if ($index >= 3 || count($recipes) >= 3) {
                break;
            }
            
            try {
                $details = $this->mealDBService->getMealDetails($log->recipe_id);
                if ($details) {
                    $recipes[] = $details;
                }
            } catch (\Exception $e) {
                continue; // Skip failed requests
            }
        }
        
        return $recipes;
    }
    
    /**
     * Get varied high-protein recipes
     */
    private function getVariedProteinRecipes()
    {
        $proteinCategories = ['Chicken', 'Beef', 'Seafood', 'Pork'];
        
        // Rotate category every 6 hours
        $hoursSinceEpoch = floor(time() / 3600);
        $categoryIndex = floor($hoursSinceEpoch / 6) % count($proteinCategories);
        $selectedCategory = $proteinCategories[$categoryIndex];
        
        // Cache for 6 hours
        return Cache::remember("protein_recipes_{$selectedCategory}", 21600, function() use ($selectedCategory) {
            $meals = $this->mealDBService->getMealsByCategory($selectedCategory, 50);
            
            if (empty($meals)) {
                return [];
            }
            
            // Shuffle with current timestamp seed
            shuffle($meals);
            
            return array_slice($meals, 0, 5);
        });
    }
    
    /**
     * Calculate BMR using Mifflin-St Jeor equation
     */
    private function calculateBMR($gender, $age, $height, $weight)
    {
        if (strtolower($gender) === 'male') {
            return (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
        } else {
            return (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
        }
    }
    
    /**
     * Get activity level multiplier
     */
    private function getActivityMultiplier($activityLevel)
    {
        $multipliers = [
            'sedentary' => 1.2,
            'lightly active' => 1.375,
            'moderately active' => 1.55,
            'extremely active' => 1.9
        ];
        
        $level = strtolower($activityLevel);
        return $multipliers[$level] ?? 1.2;
    }
    
    /**
     * Get current authenticated user from Firebase token
     */
    protected function getCurrentUser(Request $request)
    {
        $idToken = $request->session()->get('firebase_token') 
                   ?? $request->bearerToken();
        
        if (!$idToken) {
            return null;
        }

        if (!$this->firebase) {
            return null;
        }

        try {
            $verifiedIdToken = $this->firebase->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            
            return User::where('firebase_uid', $uid)->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}