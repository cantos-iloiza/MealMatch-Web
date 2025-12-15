<?php

// app/Services/TheMealDBService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheMealDBService
{
    private const BASE_URL = 'https://www.themealdb.com/api/json/v1/1';

    private static function generateCookingTime(string $mealName, string $mealId): int
    {
        $name = strtolower($mealName);
        $idHash = abs(crc32($mealId)) % 100;

        if (str_contains($name, 'sandwich') || str_contains($name, 'salad') || 
            str_contains($name, 'toast') || str_contains($name, 'smoothie')) {
            $baseTime = 15;
            $variation = 10;
        } elseif (str_contains($name, 'pasta') || str_contains($name, 'stir') || 
                 str_contains($name, 'fried') || str_contains($name, 'noodle')) {
            $baseTime = 25;
            $variation = 20;
        } elseif (str_contains($name, 'roast') || str_contains($name, 'bake') || 
                 str_contains($name, 'stew') || str_contains($name, 'curry')) {
            $baseTime = 45;
            $variation = 45;
        } else {
            $baseTime = 30;
            $variation = 20;
        }

        $calculatedTime = $baseTime + intval(($idHash * $variation) / 100);
        return self::roundToNearestFive($calculatedTime);
    }

    private static function generateServings(string $mealId): int
    {
        $idHash = abs(crc32($mealId)) % 5;
        return 2 + $idHash;
    }

    private static function generateNutrition(string $mealName, string $mealId): array
    {
        $name = strtolower($mealName);
        $idHash = abs(crc32($mealId)) % 100;

        if (str_contains($name, 'chicken') || str_contains($name, 'beef') || 
            str_contains($name, 'pork') || str_contains($name, 'fish') || 
            str_contains($name, 'lamb') || str_contains($name, 'steak')) {
            $calories = 450 + intval(($idHash * 200) / 100);
            $protein = 35 + intval(($idHash * 20) / 100);
            $carbs = 20 + intval(($idHash * 30) / 100);
            $fat = 15 + intval(($idHash * 15) / 100);
        } elseif (str_contains($name, 'pasta') || str_contains($name, 'rice') || 
                 str_contains($name, 'noodle') || str_contains($name, 'pizza')) {
            $calories = 500 + intval(($idHash * 250) / 100);
            $protein = 15 + intval(($idHash * 15) / 100);
            $carbs = 60 + intval(($idHash * 40) / 100);
            $fat = 12 + intval(($idHash * 18) / 100);
        } elseif (str_contains($name, 'salad') || str_contains($name, 'soup') || 
                 str_contains($name, 'sandwich')) {
            $calories = 250 + intval(($idHash * 200) / 100);
            $protein = 12 + intval(($idHash * 18) / 100);
            $carbs = 25 + intval(($idHash * 25) / 100);
            $fat = 8 + intval(($idHash * 12) / 100);
        } elseif (str_contains($name, 'cake') || str_contains($name, 'pie') || 
                 str_contains($name, 'pudding') || str_contains($name, 'cookie')) {
            $calories = 350 + intval(($idHash * 300) / 100);
            $protein = 4 + intval(($idHash * 6) / 100);
            $carbs = 45 + intval(($idHash * 40) / 100);
            $fat = 15 + intval(($idHash * 20) / 100);
        } else {
            $calories = 400 + intval(($idHash * 250) / 100);
            $protein = 25 + intval(($idHash * 20) / 100);
            $carbs = 35 + intval(($idHash * 30) / 100);
            $fat = 12 + intval(($idHash * 18) / 100);
        }

        return [
            'calories' => $calories,
            'protein' => "{$protein}g",
            'carbs' => "{$carbs}g",
            'fat' => "{$fat}g",
        ];
    }

    private static function parseInstructions(?string $rawInstructions): array
    {
        if (empty($rawInstructions)) return [];

        $cleaned = preg_replace('/<[^>]*>/', '', $rawInstructions);
        $cleaned = str_replace(["\r\n", "\r"], "\n", trim($cleaned));

        $steps = [];

        if (preg_match('/(?:STEP\s*\d+|Step\s*\d+|\d+\.)\s*[:\-]?\s*/i', $cleaned)) {
            $parts = preg_split('/(?:STEP\s*\d+|Step\s*\d+|\d+\.)\s*[:\-]?\s*/i', $cleaned);
            $steps = array_filter(array_map('trim', $parts));
        } else {
            $sentences = preg_split('/\.\s+(?=[A-Z])/', $cleaned);
            $currentStep = [];

            foreach ($sentences as $sentence) {
                $sentence = trim($sentence);
                if (empty($sentence)) continue;

                $currentStep[] = $sentence;

                if (count($currentStep) >= 2 || strlen($sentence) > 150) {
                    $steps[] = implode('. ', $currentStep) . '.';
                    $currentStep = [];
                }
            }

            if (!empty($currentStep)) {
                $steps[] = implode('. ', $currentStep) . '.';
            }
        }

        return array_map(fn($text, $idx) => [
            'text' => $text,
            'timer' => '00:00'
        ], $steps, array_keys($steps));
    }

    private static function roundToNearestFive(int $number): int
    {
        return round($number / 5) * 5;
    }

    public function findByIngredients(array $ingredients, int $number = 10): array
    {
        if (empty($ingredients)) return [];

        try {
            $mainIngredient = trim($ingredients[0]);
            $url = self::BASE_URL . "/filter.php?i=" . urlencode($mainIngredient);

            Log::info("API URL: $url");
            Log::info("Searching for: $mainIngredient");

            $response = Http::timeout(10)->get($url);

            Log::info("Status Code: " . $response->status());

            if ($response->successful()) {
                $data = $response->json();

                if (empty($data['meals'])) {
                    Log::info("API returned null meals");
                    return [];
                }

                $meals = $data['meals'];
                Log::info("Found " . count($meals) . " meals from API");

                $results = array_slice(array_map(function($meal) {
                    $mealName = $meal['strMeal'] ?? 'Unknown Recipe';
                    $mealId = strval($meal['idMeal'] ?? '');

                    return [
                        'id' => $mealId,
                        'title' => $mealName,
                        'image' => $meal['strMealThumb'] ?? '',
                        'missedIngredientCount' => 0,
                        'missedIngredients' => [],
                        'readyInMinutes' => self::generateCookingTime($mealName, $mealId),
                        'servings' => self::generateServings($mealId),
                        'nutrition' => self::generateNutrition($mealName, $mealId),
                    ];
                }, $meals), 0, $number);

                Log::info("Returning " . count($results) . " recipes with data");
                return $results;
            }

            Log::error("API Error: Status " . $response->status());
            return [];
        } catch (\Exception $e) {
            Log::error("Exception in findByIngredients: " . $e->getMessage());
            return [];
        }
    }

    public function getMealDetails(string $mealId): ?array
    {
        try {
            $url = self::BASE_URL . "/lookup.php?i=$mealId";
            Log::info("Getting details for meal ID: $mealId");

            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                
                if (!empty($data['meals'])) {
                    $meal = $data['meals'][0];

                    $ingredients = [];
                    for ($i = 1; $i <= 20; $i++) {
                        $ingredient = trim($meal["strIngredient$i"] ?? '');
                        $measure = trim($meal["strMeasure$i"] ?? '');

                        if (!empty($ingredient)) {
                            $ingredients[] = [
                                'name' => $ingredient,
                                'measure' => $measure,
                                'original' => !empty($measure) ? "$measure $ingredient" : $ingredient,
                            ];
                        }
                    }

                    $mealName = $meal['strMeal'] ?? 'Unknown Recipe';
                    $mealId = strval($meal['idMeal'] ?? '');
                    $cookTime = self::generateCookingTime($mealName, $mealId);
                    $instructions = self::parseInstructions($meal['strInstructions'] ?? '');

                    Log::info("Loaded details for: $mealName");

                    return [
                        'id' => $mealId,
                        'title' => $mealName,
                        'image' => $meal['strMealThumb'] ?? '',
                        'instructions' => $instructions,
                        'ingredients' => $ingredients,
                        'category' => $meal['strCategory'] ?? '',
                        'area' => $meal['strArea'] ?? '',
                        'youtubeUrl' => $meal['strYoutube'] ?? '',
                        'sourceUrl' => $meal['strSource'] ?? '',
                        'prepTime' => '10',
                        'cookTime' => strval($cookTime),
                        'readyInMinutes' => $cookTime,
                        'servings' => self::generateServings($mealId),
                        'nutrition' => self::generateNutrition($mealName, $mealId),
                        'rating' => 4.0 + ((abs(crc32($mealId)) % 100) / 100),
                        'author' => !empty($meal['strSource']) 
                            ? parse_url($meal['strSource'], PHP_URL_HOST) 
                            : 'TheMealDB Community',
                    ];
                }
            }

            Log::info("Failed to get meal details");
            return null;
        } catch (\Exception $e) {
            Log::error("Error getting meal details: " . $e->getMessage());
            return null;
        }
    }

    public function searchRecipes(string $query): array
    {
        try {
            $url = self::BASE_URL . "/search.php?s=" . urlencode($query);
            Log::info("Searching recipes: $query");

            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();

                if (empty($data['meals'])) {
                    Log::info("No meals found for: $query");
                    return [];
                }

                $meals = $data['meals'];
                Log::info("Found " . count($meals) . " recipes");

                return array_map(function($meal) {
                    $mealName = $meal['strMeal'] ?? 'Unknown Recipe';
                    $mealId = strval($meal['idMeal'] ?? '');

                    return [
                        'id' => $mealId,
                        'title' => $mealName,
                        'category' => $meal['strCategory'] ?? '',
                        'area' => $meal['strArea'] ?? '',
                        'image' => $meal['strMealThumb'] ?? '',
                        'readyInMinutes' => self::generateCookingTime($mealName, $mealId),
                        'servings' => self::generateServings($mealId),
                        'rating' => 4.0 + ((abs(crc32($mealId)) % 100) / 100),
                        'nutrition' => self::generateNutrition($mealName, $mealId),
                    ];
                }, $meals);
            }

            return [];
        } catch (\Exception $e) {
            Log::error("Error searching recipes: " . $e->getMessage());
            return [];
        }
    }
}