<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use App\Models\Favorite; // Import the Model
use Illuminate\Support\Facades\Auth;

class RecipeController extends Controller
{
    public function index(Request $request)
    {
        $tab = $request->query('tab', 'discovery');
        $category = $request->query('category');
        $search = $request->query('search');
        $recipes = [];
        
        // Get current user ID (or use 0 for guest testing)
        $userId = Auth::id() ?? 0;

        // 1. DISCOVERY TAB
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
        
        // 2. FAVORITES TAB (REAL DATABASE DATA)
        elseif ($tab === 'favorites') {
            // Fetch from DB and map to match API format
            $favs = Favorite::where('user_id', $userId)->orderBy('created_at', 'desc')->get();
            
            $recipes = $favs->map(function($fav) {
                return [
                    'idMeal' => $fav->recipe_id,
                    'strMeal' => $fav->title,
                    'strMealThumb' => $fav->image,
                    'strCategory' => $fav->category
                ];
            })->toArray();
        }

        // Get list of favorited IDs to check which hearts should be filled
        $favoriteIds = Favorite::where('user_id', $userId)->pluck('recipe_id')->toArray();

        return view('recipe', [
            'recipes' => $recipes,
            'currentCategory' => $category,
            'currentTab' => $tab,
            'favoriteIds' => $favoriteIds // Pass this to view
        ]);
    }

    // NEW: Toggle Favorite (AJAX)
    public function toggleFavorite(Request $request)
    {
        $userId = Auth::id() ?? 0;
        $recipeId = $request->input('recipe_id');

        // Check if already favorited
        $exists = Favorite::where('user_id', $userId)->where('recipe_id', $recipeId)->first();

        if ($exists) {
            // Remove it
            $exists->delete();
            return response()->json(['status' => 'removed']);
        } else {
            // Add it
            Favorite::create([
                'user_id' => $userId,
                'recipe_id' => $recipeId,
                'title' => $request->input('title'),
                'image' => $request->input('image'),
                'category' => $request->input('category'),
            ]);
            return response()->json(['status' => 'added']);
        }
    }

    // ... Keep random() and show() methods EXACTLY as they were ...
    public function random() { /* ... your existing random code ... */ }
    public function show($id) { /* ... your existing show code ... */ }
}