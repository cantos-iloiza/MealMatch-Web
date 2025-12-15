<?php

// app/Services/FoodApiService.php
namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class FoodApiService
{
    private const USDA_API_KEY = 'QGUrntEEaTNFFHkrLRk7h8Lr8Vh9Uf6O5DVGhDsV';
    private const USDA_BASE_URL = 'https://api.nal.usda.gov/fdc/v1';
    private const OFF_BASE_URL = 'https://world.openfoodfacts.org/cgi';

    public function searchAllSources(string $query): array
    {
        $results = [];
        
        $usdaResults = $this->searchUsdaFoods($query);
        $results = array_merge($results, $usdaResults);
        
        $offResults = $this->searchOpenFoodFacts($query);
        $results = array_merge($results, $offResults);
        
        Log::info("Total results from all sources: " . count($results));
        
        return $results;
    }

    public function searchUsdaFoods(string $query): array
    {
        try {
            $url = self::USDA_BASE_URL . '/foods/search?' . http_build_query([
                'query' => $query,
                'pageSize' => 10,
                'api_key' => self::USDA_API_KEY,
            ]);

            Log::info("Searching USDA: $query");

            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $foods = $data['foods'] ?? [];

                Log::info("USDA found " . count($foods) . " items");

                return array_map([$this, 'parseUsdaFood'], $foods);
            }

            Log::error("USDA API Error: " . $response->status());
            return [];
        } catch (\Exception $e) {
            Log::error("Error searching USDA foods: " . $e->getMessage());
            return [];
        }
    }

    private function parseUsdaFood(array $usdaFood): array
    {
        $nutrients = $usdaFood['foodNutrients'] ?? [];

        $getUsdaNutrient = function($nutrientId) use ($nutrients) {
            foreach ($nutrients as $nutrient) {
                if (($nutrient['nutrientId'] ?? 0) == $nutrientId) {
                    return floatval($nutrient['value'] ?? 0);
                }
            }
            return 0.0;
        };

        $servingAmount = 100.0;
        $servingUnit = 'g';

        if (!empty($usdaFood['householdServingFullText'])) {
            $householdServing = $usdaFood['householdServingFullText'];
            if (preg_match('/(\d+\.?\d*)\s*([a-zA-Z]+)/', $householdServing, $matches)) {
                $servingAmount = floatval($matches[1] ?? 1);
                $servingUnit = $matches[2] ?? 'serving';
            }
        } elseif (!empty($usdaFood['servingSize'])) {
            $servingAmount = floatval($usdaFood['servingSize'] ?? 100);
            $servingUnit = $usdaFood['servingSizeUnit'] ?? 'g';
        }

        return [
            'name' => $usdaFood['description'] ?? 'Unknown',
            'brand' => $usdaFood['brandOwner'] ?? '',
            'calories' => $getUsdaNutrient(1008),
            'carbs' => $getUsdaNutrient(1005),
            'protein' => $getUsdaNutrient(1003),
            'fat' => $getUsdaNutrient(1004),
            'servingsamount' => $servingAmount,
            'servingsize' => $servingUnit,
            'source' => 'USDA',
            'usdaFdcId' => $usdaFood['fdcId'] ?? null,
        ];
    }

    public function searchOpenFoodFacts(string $query): array
    {
        try {
            $url = self::OFF_BASE_URL . '/search.pl?' . http_build_query([
                'search_terms' => $query,
                'page_size' => 20,
                'json' => 1,
            ]);

            Log::info("Searching Open Food Facts: $query");

            $response = Http::timeout(10)->get($url);

            if ($response->successful()) {
                $data = $response->json();
                $products = $data['products'] ?? [];

                Log::info("OFF returned " . count($products) . " products");

                $parsedProducts = [];

                foreach ($products as $product) {
                    try {
                        $parsed = $this->parseOpenFoodFactsProduct($product);

                        if ($parsed['name'] !== 'Unknown Product' && 
                            ($parsed['calories'] > 0 || $parsed['carbs'] > 0 || $parsed['protein'] > 0)) {
                            $parsedProducts[] = $parsed;
                        }
                    } catch (\Exception $e) {
                        Log::error("Error parsing product: " . $e->getMessage());
                    }
                }

                Log::info("Final count: " . count($parsedProducts) . " valid products");
                return $parsedProducts;
            }

            Log::error("OFF API Error: " . $response->status());
            return [];
        } catch (\Exception $e) {
            Log::error("Error searching Open Food Facts: " . $e->getMessage());
            return [];
        }
    }

    private function parseOpenFoodFactsProduct(array $product): array
    {
        $nutriments = $product['nutriments'] ?? [];

        $getNutriment = function($key) use ($nutriments) {
            $keys = [
                "{$key}_100g",
                "{$key}-100g",
                $key,
                "{$key}_serving"
            ];

            foreach ($keys as $k) {
                if (isset($nutriments[$k])) {
                    $value = $nutriments[$k];
                    return is_numeric($value) ? floatval($value) : 0.0;
                }
            }

            return 0.0;
        };

        $productName = $product['product_name'] ?? 
                      $product['product_name_en'] ?? 
                      $product['generic_name'] ?? 
                      'Unknown Product';

        $brand = $product['brands'] ?? $product['brand_owner'] ?? '';

        $servingSize = 'g';
        $servingAmount = 100.0;

        if (!empty($product['serving_size'])) {
            $serving = $product['serving_size'];
            if (preg_match('/(\d+\.?\d*)\s*([a-zA-Z]+)/', $serving, $matches)) {
                $servingAmount = floatval($matches[1] ?? 100);
                $servingSize = $matches[2] ?? 'g';
            }
        } elseif (!empty($product['quantity'])) {
            $quantity = $product['quantity'];
            if (preg_match('/(\d+\.?\d*)\s*([a-zA-Z]+)/', $quantity, $matches)) {
                $servingAmount = floatval($matches[1] ?? 100);
                $servingSize = $matches[2] ?? 'g';
            }
        }

        $calories = $getNutriment('energy-kcal');
        if ($calories == 0) {
            $energyKj = $getNutriment('energy-kj');
            if ($energyKj > 0) {
                $calories = $energyKj / 4.184;
            } else {
                $calories = $getNutriment('energy') / 4.184;
            }
        }

        return [
            'name' => $productName,
            'brand' => $brand,
            'calories' => $calories,
            'carbs' => $getNutriment('carbohydrates'),
            'protein' => $getNutriment('proteins'),
            'fat' => $getNutriment('fat'),
            'servingsamount' => $servingAmount,
            'servingsize' => $servingSize,
            'source' => 'OpenFoodFacts',
            'barcode' => $product['code'] ?? '',
        ];
    }

    public function getProductByBarcode(string $barcode): ?array
    {
        try {
            $url = "https://world.openfoodfacts.org/api/v2/product/{$barcode}.json";
            $response = Http::get($url);

            if ($response->successful()) {
                $data = $response->json();
                if (($data['status'] ?? 0) == 1) {
                    return $this->parseOpenFoodFactsProduct($data['product']);
                }
            }

            return null;
        } catch (\Exception $e) {
            Log::error("Error getting product by barcode: " . $e->getMessage());
            return null;
        }
    }
}