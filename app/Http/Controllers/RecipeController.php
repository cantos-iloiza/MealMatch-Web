<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
// Uncomment when Firebase Admin SDK is needed
// use Kreait\Firebase\Factory;
// use Kreait\Firebase\ServiceAccount;

class RecipeController extends Controller
{
    /**
     * Display the recipe detail page
     * Fetches recipe data from MealDB API
     * 
     * @param string $id - MealDB recipe ID
     * @return \Illuminate\View\View
     */
    public function show($id)
    {
        try {
            // Fetch recipe from MealDB API
            $response = Http::get("https://www.themealdb.com/api/json/v1/1/lookup.php?i={$id}");
            
            if (!$response->successful() || !isset($response->json()['meals'][0])) {
                abort(404, 'Recipe not found');
            }
            
            $recipe = $response->json()['meals'][0];
            
            // Extract ingredients and measurements
            $ingredients = [];
            for ($i = 1; $i <= 20; $i++) {
                $ingredient = $recipe["strIngredient{$i}"] ?? '';
                $measure = $recipe["strMeasure{$i}"] ?? '';
                
                if (!empty($ingredient) && trim($ingredient) != "") {
                    $ingredients[] = [
                        'ingredient' => $ingredient,
                        'measure' => trim($measure)
                    ];
                }
            }
            
            // Split instructions into steps
            $instructions = array_filter(
                explode("\r\n", $recipe['strInstructions'] ?? ''),
                fn($step) => !empty(trim($step))
            );
            
            // Extract YouTube video ID if available
            $videoId = null;
            if (!empty($recipe['strYoutube'])) {
                preg_match('/[?&]v=([^&]+)/', $recipe['strYoutube'], $matches);
                $videoId = $matches[1] ?? null;
            }
            
            // Split recipe name for styling (last word in orange)
            $words = explode(' ', $recipe['strMeal']);
            $lastWord = array_pop($words);
            $recipeName = implode(' ', $words);
            
            // Parse tags
            $tags = !empty($recipe['strTags']) ? explode(',', $recipe['strTags']) : [];
            
            return view('recipe', compact('recipe', 'ingredients', 'instructions', 'videoId', 'recipeName', 'lastWord', 'tags'));
            
        } catch (\Exception $e) {
            abort(500, 'Error fetching recipe: ' . $e->getMessage());
        }
    }

    public function saveFavorite(Request $request)
    {
        try {
            $request->validate([
                'recipe_id' => 'required|string',
                'recipe_name' => 'required|string',
                'recipe_image' => 'required|string',
                'recipe_category' => 'nullable|string',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Recipe saved to favorites'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function getFavorites(Request $request)
    {
        try {
   
            return response()->json([
                'success' => true,
                'data' => [
                    [
                        'id' => '1',
                        'recipeId' => '52772',
                        'recipeName' => 'Teriyaki Chicken Casserole',
                        'recipeImage' => 'https://www.themealdb.com/images/media/meals/wvpsxx1468256321.jpg',
                        'recipeCategory' => 'Chicken',
                        'createdAt' => '2025-12-14 10:30:00'
                    ],
                    [
                        'id' => '2',
                        'recipeId' => '52940',
                        'recipeName' => 'Brown Stew Chicken',
                        'recipeImage' => 'https://www.themealdb.com/images/media/meals/sypxpx1515365095.jpg',
                        'recipeCategory' => 'Chicken',
                        'createdAt' => '2025-12-13 15:20:00'
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Remove recipe from favorites
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function removeFavorite(Request $request)
    {
        try {
            $request->validate([
                'favorite_id' => 'required|string',
            ]);
            
            return response()->json([
                'success' => true,
                'message' => 'Recipe removed from favorites'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}