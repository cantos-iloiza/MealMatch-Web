<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Session;

class RecipeController extends Controller
{
    // 1. INDEX (Discovery & Favorites)
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'discovery');
        $category = $request->query('category');
        $search = $request->query('search');
        $recipes = [];
        
        if ($tab === 'discovery') {
            try {
                $baseUrl = "https://www.themealdb.com/api/json/v1/1/";
                if ($category) {
                    $response = Http::withoutVerifying()->get($baseUrl . "filter.php?c={$category}");
                } elseif ($search) {
                    $response = Http::withoutVerifying()->get($baseUrl . "search.php?s={$search}");
                } else {
                    $response = Http::withoutVerifying()->get($baseUrl . "filter.php?c=Chicken");
                }
                
                if ($response->successful()) {
                    $recipes = $response->json()['meals'] ?? [];
                }
            } catch (\Exception $e) {
                $recipes = [];
            }
        } 
        elseif ($tab === 'favorites') {
            $recipes = Session::get('favorites', []);
        }

        $favs = Session::get('favorites', []);
        $favoriteIds = array_column($favs, 'idMeal');

        return view('recipe', [
            'recipes' => $recipes,
            'currentCategory' => $category,
            'currentTab' => $tab,
            'favoriteIds' => $favoriteIds
        ]);
    }

    // 2. SHOW DETAILS (SMART ESTIMATOR + 5 MIN ROUNDING)
    public function show($id)
    {
        try {
            $response = Http::withoutVerifying()->get("https://www.themealdb.com/api/json/v1/1/lookup.php?i={$id}");
            $apiMeal = $response->json()['meals'][0] ?? null;
        } catch (\Exception $e) {
            $apiMeal = null;
        }

        if (!$apiMeal) {
            return redirect()->route('recipes.index')->with('error', 'Recipe not found');
        }

        // --- A. ANALYZE THE RECIPE ---
        // 1. Get Ingredient Count
        $ingredientCount = 0;
        $ingredients = [];
        for ($i = 1; $i <= 20; $i++) {
            if (!empty($apiMeal["strIngredient$i"])) {
                $ingredients[] = $apiMeal["strMeasure$i"] . ' ' . $apiMeal["strIngredient$i"];
                $ingredientCount++;
            }
        }

        // 2. Determine Category characteristics
        $cat = $apiMeal['strCategory'] ?? 'Miscellaneous';
        
        // Base stats [Calories, Base Cook Time]
        $categoryStats = [
            'Beef' => [750, 60], 'Pork' => [700, 50], 'Lamb' => [720, 60],
            'Chicken' => [550, 40], 'Turkey' => [500, 45],
            'Seafood' => [350, 20], 'Fish' => [320, 20],
            'Pasta' => [600, 25], 'Rice' => [450, 25],
            'Vegetarian' => [380, 30], 'Vegan' => [350, 30],
            'Dessert' => [450, 40], 'Starter' => [250, 15], 'Side' => [200, 15],
            'Breakfast' => [400, 15]
        ];

        $stats = $categoryStats[$cat] ?? [500, 30]; 
        $baseCals = $stats[0];
        $baseTime = $stats[1];

        // --- B. CALCULATE VALUES (MULTIPLES OF 5) ---
        
        // 1. Prep Time: Base calculation -> Round UP to nearest 5
        $rawPrep = 5 + ($ingredientCount * 2);
        $prepTimeVal = ceil($rawPrep / 5) * 5; 
        
        // 2. Cook Time: Base time + Random variance (in steps of 5)
        // rand(-2, 4) * 5 gives values like -10, -5, 0, 5, 10, 15, 20
        $cookTimeVal = $baseTime + (rand(-2, 4) * 5);
        if ($cookTimeVal < 10) $cookTimeVal = 10; // Minimum 10 mins

        // 3. Difficulty
        if ($ingredientCount <= 6) $difficulty = 'Easy';
        elseif ($ingredientCount <= 12) $difficulty = 'Medium';
        else $difficulty = 'Advanced';

        // 4. Calories
        $caloriesVal = $baseCals + ($ingredientCount * 10) + rand(-20, 20);
        // Round calories to nearest 10 for cleaner look
        $caloriesVal = round($caloriesVal / 10) * 10; 

        // --- C. TEXT FORMATTING ---
        $nameParts = explode(' ', $apiMeal['strMeal']);
        $highlight = (count($nameParts) > 1) ? array_pop($nameParts) : ''; 
        $title = implode(' ', $nameParts);
        if (empty($title)) $title = $apiMeal['strMeal'];

        // Instructions formatting
        $instr = $apiMeal['strInstructions'];
        $rawInstructions = preg_split('/(\r\n|\r|\n)/', $instr);
        if (count($rawInstructions) < 2) $rawInstructions = explode('. ', $instr);

        $formattedInstructions = [];
        $stepCounter = 1;
        foreach ($rawInstructions as $line) {
            $line = trim($line);
            if ($line !== '' && $line !== '.') {
                $formattedInstructions[] = [
                    'step' => str_pad($stepCounter, 2, '0', STR_PAD_LEFT),
                    'text' => $line . (str_ends_with($line, '.') ? '' : '.')
                ];
                $stepCounter++;
            }
        }

        $area = $apiMeal['strArea'] ?? 'International';
        $simpleDescription = "Experience the authentic taste of this {$area} {$cat}. This {$apiMeal['strMeal']} recipe brings together fresh ingredients and bold flavors to create a meal that is perfect for any occasion.";

        $tags = !empty($apiMeal['strTags']) ? explode(',', $apiMeal['strTags']) : ['Delicious', 'Homemade'];

        $recipeData = [
            'id' => $apiMeal['idMeal'],
            'image' => $apiMeal['strMealThumb'],
            'title' => $title,
            'highlight' => $highlight,
            'subtitle' => ($cat) . ' • ' . ($area),
            'description' => $simpleDescription,
            
            // SMART VALUES
            'cuisine' => $area,
            'servings' => rand(2, 4) . ' People',
            'prep_time' => $prepTimeVal . ' mins', // Will be 15, 20, 25...
            'cook_time' => $cookTimeVal . ' mins', // Will be 30, 35, 40...
            'difficulty' => $difficulty,
            
            'ingredients' => $ingredients,
            'instructions' => $formattedInstructions,
            'tags' => $tags,
            'nutrition' => [
                ['label' => 'Calories', 'value' => $caloriesVal . ' kcal'],
                ['label' => 'Carbs', 'value' => rand(20, 80) . 'g'],
                ['label' => 'Protein', 'value' => rand(15, 60) . 'g'],
                ['label' => 'Fat', 'value' => rand(10, 40) . 'g'],
            ],
            'author' => [
                'name' => 'Chef ' . ($area),
                'image' => 'https://ui-avatars.com/api/?name=Chef&background=random'
            ]
        ];

        return view('recipe-detail', ['recipe' => $recipeData]);
    }

    // 3. TOGGLE FAVORITE
    public function toggleFavorite(Request $request)
    {
        $id = $request->input('recipe_id');
        $favs = Session::get('favorites', []);
        $key = array_search($id, array_column($favs, 'idMeal'));

        if ($key !== false) {
            unset($favs[$key]);
            $status = 'removed';
        } else {
            $favs[] = [
                'idMeal' => $id,
                'strMeal' => $request->input('title'),
                'strMealThumb' => $request->input('image'),
                'strCategory' => $request->input('category'),
            ];
            $status = 'added';
        }

        Session::put('favorites', array_values($favs));
        return response()->json(['status' => $status]);
    }
}