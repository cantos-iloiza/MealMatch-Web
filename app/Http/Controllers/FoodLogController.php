<?php

// app/Http/Controllers/FoodLogController.php
namespace App\Http\Controllers;

use App\Models\MealLog;
use App\Models\User;
use App\Models\Recipe;
use App\Models\Favorite;
use App\Services\FoodApiService;
use App\Services\TheMealDBService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Kreait\Firebase\Factory;

class FoodLogController extends Controller
{
    protected $foodApiService;
    protected $mealDbService;
    protected $firebase;

    public function __construct(FoodApiService $foodApiService, TheMealDBService $mealDbService)
    {
        $this->foodApiService = $foodApiService;
        $this->mealDbService = $mealDbService;
        
        // Initialize Firebase Admin SDK
        $this->firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'))
            ->createAuth();
    }

    public function index(Request $request)
    {
        $user = $this->getCurrentUser($request);
        
        if (!$user) {
            return redirect()->route('login');
        }

        $selectedMeal = $request->session()->get('selected_meal');
        
        // Get today's consumed calories
        $todayCalories = MealLog::where('firebase_uid', $user->firebase_uid)
            ->whereDate('date', today())
            ->sum('calories');

        // Check if over calorie goal
        $isOverGoal = $todayCalories > $user->calorie_goal;
        $caloriesOver = $isOverGoal ? ($todayCalories - $user->calorie_goal) : 0;

        // Load recent foods (last 50 meal logs, unique by food name)
        $recentFoods = MealLog::where('firebase_uid', $user->firebase_uid)
            ->orderBy('created_at', 'desc')
            ->limit(50)
            ->get()
            ->unique('food_name')
            ->take(7)
            ->values();

        return view('food-log.index', compact(
            'user', 
            'selectedMeal', 
            'todayCalories', 
            'isOverGoal', 
            'caloriesOver',
            'recentFoods'
        ));
    }

    public function search(Request $request)
    {
        $query = $request->input('query');
        
        if (empty(trim($query))) {
            return response()->json([
                'success' => true,
                'results' => [],
                'message' => 'Search for foods to get started'
            ]);
        }

        $results = $this->foodApiService->searchAllSources($query);

        return response()->json([
            'success' => true,
            'results' => $results,
            'message' => empty($results) ? 'No results found. Try different keywords.' : ''
        ]);
    }

    public function selectMeal(Request $request)
    {
        $meal = $request->input('meal');
        $request->session()->put('selected_meal', $meal);

        return response()->json(['success' => true, 'meal' => $meal]);
    }

    public function addToMeal(Request $request)
    {
        $user = $this->getCurrentUser($request);
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $validated = $request->validate([
            'category' => 'required|string',
            'food_name' => 'required|string',
            'calories' => 'required|numeric',
            'carbs' => 'required|numeric',
            'fats' => 'required|numeric',
            'proteins' => 'required|numeric',
            'serving' => 'required|string',
            'brand' => 'nullable|string',
            'is_verified' => 'boolean',
            'source' => 'nullable|string',
        ]);

        $validated['firebase_uid'] = $user->firebase_uid;
        $validated['date'] = today();

        MealLog::create($validated);

        // Update today's consumed calories
        $todayCalories = MealLog::where('firebase_uid', $user->firebase_uid)
            ->whereDate('date', today())
            ->sum('calories');

        return response()->json([
            'success' => true,
            'message' => "{$validated['food_name']} added to {$validated['category']}!",
            'today_calories' => $todayCalories
        ]);
    }

    // Get favorites for current user
    public function favorites(Request $request)
    {
        $user = $this->getCurrentUser($request);
        
        if (!$user) {
            return response()->json(['success' => false, 'favorites' => []]);
        }

        $favoriteIds = Favorite::where('firebase_uid', $user->firebase_uid)
            ->pluck('recipe_id')
            ->toArray();

        $recipes = [];
        foreach ($favoriteIds as $id) {
            $recipe = $this->mealDbService->getMealDetails($id);
            if ($recipe) {
                $recipes[] = $recipe;
            }
        }

        return response()->json(['success' => true, 'favorites' => $recipes]);
    }

    // Get user's custom recipes
    public function myRecipes(Request $request)
    {
        $user = $this->getCurrentUser($request);
        
        if (!$user) {
            return response()->json(['success' => false, 'recipes' => []]);
        }

        $recipes = Recipe::where('firebase_uid', $user->firebase_uid)
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function($recipe) {
                // Calculate calories if not set
                if (!$recipe->calories && $recipe->nutrients) {
                    $nutrients = $recipe->nutrients;
                    $protein = $nutrients['Protein'] ?? 0;
                    $carbs = $nutrients['Carbs'] ?? 0;
                    $fat = $nutrients['Fat'] ?? 0;
                    $recipe->calories = round(($protein * 4) + ($carbs * 4) + ($fat * 9));
                }
                return $recipe;
            });

        return response()->json(['success' => true, 'recipes' => $recipes]);
    }

    // Add recipe to meal (favorites or custom recipes)
    public function addRecipeToMeal(Request $request)
    {
        $user = $this->getCurrentUser($request);
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $validated = $request->validate([
            'category' => 'required|string',
            'recipe_id' => 'required|string',
            'recipe_name' => 'required|string',
            'calories' => 'required|integer',
        ]);

        MealLog::create([
            'firebase_uid' => $user->firebase_uid,
            'category' => $validated['category'],
            'food_name' => $validated['recipe_name'],
            'calories' => $validated['calories'],
            'carbs' => 0,
            'fats' => 0,
            'proteins' => 0,
            'serving' => '1 serving',
            'date' => today(),
            'recipe_id' => $validated['recipe_id'],
        ]);

        return response()->json([
            'success' => true,
            'message' => "{$validated['recipe_name']} added to {$validated['category']}!"
        ]);
    }

    protected function getCurrentUser(Request $request)
    {
        // Get Firebase token from session or header
        $idToken = $request->session()->get('firebase_token') 
                   ?? $request->bearerToken();
        
        if (!$idToken) {
            return null;
        }

        try {
            // Verify Firebase token
            $verifiedIdToken = $this->firebase->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            
            // Get user from database
            return User::where('firebase_uid', $uid)->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}