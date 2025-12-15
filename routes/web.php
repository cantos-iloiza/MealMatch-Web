<?php

use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Factory;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-firebase', function () {
    try {
        // Version 7.x syntax
        $factory = (new Factory)->withServiceAccount(
            storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
        );
        
        $database = $factory->createDatabase();
        
        return response()->json([
            'status' => 'success',
            'message' => 'Firebase connection working!',
            'version' => '7.24.0'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ]);
    }
});

// Profile routes
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

// Recipe routes
Route::get('/recipe/{id}', [RecipeController::class, 'show'])->name('recipe.show');

// Recipe API endpoints (for favorites functionality)
Route::post('/api/recipe/favorite', [RecipeController::class, 'saveFavorite'])->name('recipe.favorite.save');
Route::get('/api/recipe/favorites', [RecipeController::class, 'getFavorites'])->name('recipe.favorites');
Route::delete('/api/recipe/favorite', [RecipeController::class, 'removeFavorite'])->name('recipe.favorite.remove');