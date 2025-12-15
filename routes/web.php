<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\FoodLogController;
use App\Http\Controllers\ModifyFoodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;

// Home routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/load-recipes', [HomeController::class, 'loadRecipes'])->name('load-recipes');
Route::get('/refresh-calories', [HomeController::class, 'refreshCalories'])->name('refresh-calories');
Route::get('/recipe/{id}', function($id) {
    return view('recipe-detail', compact('id'));
})->name('recipe.show');

// Notifications route
Route::get('/notifications', function () {
    return view('notifications');
})->name('notifications');

// What Can I Cook route (ADD THIS)
Route::get('/what-can-i-cook', function () {
    return view('whatcanicook');
})->name('whatcanicook');

// Food log routes
Route::get('/food-log', [FoodLogController::class, 'index'])->name('food-log.index');
Route::post('/food-log/select-meal', [FoodLogController::class, 'selectMeal'])->name('food-log.select-meal');
Route::post('/food-log/add', [FoodLogController::class, 'addToMeal'])->name('food-log.add');
Route::get('/food-log/search', [FoodLogController::class, 'search'])->name('food-log.search');
Route::get('/food-log/favorites', [FoodLogController::class, 'favorites'])->name('food-log.favorites');
Route::get('/food-log/my-recipes', [FoodLogController::class, 'myRecipes'])->name('food-log.my-recipes');
Route::post('/food-log/add-recipe', [FoodLogController::class, 'addRecipeToMeal'])->name('food-log.add-recipe');

// Modify food routes
Route::get('/modify-food', [ModifyFoodController::class, 'show'])->name('modify-food.show');
Route::post('/modify-food/set-item', [ModifyFoodController::class, 'setFoodItem'])->name('modify-food.set-item');
Route::post('/modify-food/add', [ModifyFoodController::class, 'addFood'])->name('modify-food.add');

// Profile routes
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

// Recipe routes
Route::get('/recipe/{id}', [RecipeController::class, 'show'])->name('recipe.show');

// Recipe API endpoints (for favorites functionality)
Route::post('/api/recipe/favorite', [RecipeController::class, 'saveFavorite'])->name('recipe.favorite.save');
Route::get('/api/recipe/favorites', [RecipeController::class, 'getFavorites'])->name('recipe.favorites');
Route::delete('/api/recipe/favorite', [RecipeController::class, 'removeFavorite'])->name('recipe.favorite.remove');