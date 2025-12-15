<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatCanICookController extends Controller
{
    /**
     * Display the What Can I Cook page
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('whatcanicook');
    }
    
    /**
     * Search recipes by ingredients
     * Returns both complete matches and partial matches
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function searchByIngredients(Request $request)
    {
        try {
            $request->validate([
                'ingredients' => 'required|string',
            ]);
            
            // Parse ingredients (comma-separated)
            $userIngredients = array_map('trim', explode(',', strtolower($request->ingredients)));
            $userIngredients = array_filter($userIngredients); // Remove empty values
            
            if (empty($userIngredients)) {
                return response()->json([
                    'success' => false,
                    'message' => 'Please enter at least one ingredient'
                ], 400);
            }
            
            // Fetch recipes for each ingredient from MealDB API
            $allRecipes = [];
            $recipesByIngredient = [];
            
            foreach ($userIngredients as $ingredient) {
                $response = Http::get("https://www.themealdb.com/api/json/v1/1/filter.php?i={$ingredient}");
                
                if ($response->successful() && isset($response->json()['meals'])) {
                    $meals = $response->json()['meals'];
                    $recipesByIngredient[$ingredient] = $meals;
                    
                    foreach ($meals as $meal) {
                        $mealId = $meal['idMeal'];
                        if (!isset($allRecipes[$mealId])) {
                            $allRecipes[$mealId] = [
                                'id' => $mealId,
                                'name' => $meal['strMeal'],
                                'image' => $meal['strMealThumb'],
                                'matchedIngredients' => []
                            ];
                        }
                        $allRecipes[$mealId]['matchedIngredients'][] = $ingredient;
                    }
                }
            }
            
            // Categorize recipes by match percentage
            $completeMatches = [];
            $partialMatches = [];
            
            foreach ($allRecipes as $recipe) {
                // Fetch full recipe details to get all ingredients
                $detailResponse = Http::get("https://www.themealdb.com/api/json/v1/1/lookup.php?i={$recipe['id']}");
                
                if ($detailResponse->successful() && isset($detailResponse->json()['meals'][0])) {
                    $fullRecipe = $detailResponse->json()['meals'][0];
                    
                    // Extract all ingredients from the recipe
                    $recipeIngredients = [];
                    for ($i = 1; $i <= 20; $i++) {
                        $ingredient = $fullRecipe["strIngredient{$i}"] ?? '';
                        if (!empty($ingredient) && trim($ingredient) != "") {
                            $recipeIngredients[] = strtolower(trim($ingredient));
                        }
                    }
                    
                    $totalIngredients = count($recipeIngredients);
                    $matchedCount = count($recipe['matchedIngredients']);
                    $matchPercentage = ($matchedCount / $totalIngredients) * 100;
                    
                    $recipeData = [
                        'id' => $recipe['id'],
                        'name' => $recipe['name'],
                        'image' => $recipe['image'],
                        'category' => $fullRecipe['strCategory'] ?? '',
                        'area' => $fullRecipe['strArea'] ?? '',
                        'matchedIngredients' => $recipe['matchedIngredients'],
                        'totalIngredients' => $totalIngredients,
                        'matchedCount' => $matchedCount,
                        'matchPercentage' => round($matchPercentage, 0),
                        'missingCount' => $totalIngredients - $matchedCount,
                        'allIngredients' => $recipeIngredients
                    ];
                    
                    // 100% match = complete match
                    if ($matchPercentage >= 100) {
                        $completeMatches[] = $recipeData;
                    } else {
                        $partialMatches[] = $recipeData;
                    }
                }
            }
            
            // Sort partial matches by match percentage (highest first)
            usort($partialMatches, function($a, $b) {
                return $b['matchPercentage'] - $a['matchPercentage'];
            });
            
            return response()->json([
                'success' => true,
                'data' => [
                    'searchedIngredients' => $userIngredients,
                    'completeMatches' => $completeMatches,
                    'partialMatches' => $partialMatches,
                    'totalResults' => count($completeMatches) + count($partialMatches)
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error searching recipes: ' . $e->getMessage()
            ], 500);
        }
    }
}