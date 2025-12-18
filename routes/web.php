<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\OnboardingController;
use App\Http\Controllers\FoodLogController;
use App\Http\Controllers\ModifyFoodController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RecipeController;
use App\Http\Controllers\WhatCanICookController;

// --- 1. Authentication & Landing (The Missing 'login' Route) ---
Route::get('/', function () {
    // If already logged in, go to home
    if (session('firebase_uid')) {
        return redirect()->route('home');
    }
    return view('login'); // Loads resources/views/login.blade.php
})->name('login');

// Auth Actions
Route::post('/session-login', [AuthController::class, 'sessionLogin'])->name('auth.session-login');
Route::get('/logout', [AuthController::class, 'logout'])->name('logout');
Route::post('/register', [AuthController::class, 'store'])->name('register.store'); // Backend fallback

// --- 2. Onboarding ---
Route::get('/onboarding', [OnboardingController::class, 'show'])->name('onboarding.index');
Route::post('/onboarding', [OnboardingController::class, 'store'])->name('onboarding.store');

// --- 3. Protected Routes (Require Login) ---
Route::middleware(['web'])->group(function () {
    
    // Main Dashboard
    Route::get('/home', [HomeController::class, 'index'])->name('home');

    // Home AJAX endpoints
    Route::get('/load-recipes', [HomeController::class, 'loadRecipes'])->name('load-recipes');
    Route::get('/refresh-calories', [HomeController::class, 'refreshCalories'])->name('refresh-calories');

    // Static Pages
    Route::view('/settings', 'settings')->name('settings');
    Route::view('/aboutus', 'aboutus')->name('aboutus');
    Route::view('/usermanual', 'usermanual')->name('usermanual');
    Route::view('/notifications', 'notifications')->name('notifications');

    // What Can I Cook
    Route::get('/what-can-i-cook', [WhatCanICookController::class, 'index'])->name('whatcanicook');
    Route::post('/api/what-can-i-cook/search', [WhatCanICookController::class, 'searchByIngredients'])->name('whatcanicook.search');

    // Food Log
    Route::get('/food-log', [FoodLogController::class, 'index'])->name('food-log.index');
    Route::post('/food-log/select-meal', [FoodLogController::class, 'selectMeal'])->name('food-log.select-meal');
    Route::post('/food-log/add', [FoodLogController::class, 'addToMeal'])->name('food-log.add');
    Route::get('/food-log/search', [FoodLogController::class, 'search'])->name('food-log.search');
    Route::get('/food-log/favorites', [FoodLogController::class, 'favorites'])->name('food-log.favorites');
    Route::get('/food-log/my-recipes', [FoodLogController::class, 'myRecipes'])->name('food-log.my-recipes');
    Route::post('/food-log/add-recipe', [FoodLogController::class, 'addRecipeToMeal'])->name('food-log.add-recipe');

    // Modify Food
    Route::get('/modify-food', [ModifyFoodController::class, 'show'])->name('modify-food.show');
    Route::post('/modify-food/set-item', [ModifyFoodController::class, 'setFoodItem'])->name('modify-food.set-item');
    Route::post('/modify-food/add', [ModifyFoodController::class, 'addFood'])->name('modify-food.add');

    // Recipes
    Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');
    Route::get('/recipe/{id}', [RecipeController::class, 'show'])->name('recipe.show');
    
    // Recipe Favorites
    Route::post('/recipe/favorite', [RecipeController::class, 'toggleFavorite'])->name('recipe.toggle');
    Route::post('/api/recipe/favorite', [RecipeController::class, 'saveFavorite'])->name('recipe.favorite.save');
    Route::get('/api/recipe/favorites', [RecipeController::class, 'getFavorites'])->name('recipe.favorites');
    Route::delete('/api/recipe/favorite', [RecipeController::class, 'removeFavorite'])->name('recipe.favorite.remove');


// Profile page API endpoints
Route::get('/api/profile/user-data', [ProfileController::class, 'getUserData'])->name('profile.user.data');
Route::get('/api/profile/weekly-streak', [ProfileController::class, 'getWeeklyStreak'])->name('profile.weekly.streak');
Route::get('/api/profile/highest-streak', [ProfileController::class, 'getHighestStreak'])->name('profile.highest.streak');
Route::get('/api/profile/average-calories', [ProfileController::class, 'getAverageCalories'])->name('profile.average.calories');
Route::get('/api/profile/logs-by-date', [ProfileController::class, 'getLogsGroupedByCategory'])->name('profile.logs.by.date');
Route::get('/api/profile/logs-range', [ProfileController::class, 'getLogsInRange'])->name('profile.logs.range');
Route::get('/api/profile/logs-in-range', [ProfileController::class, 'getLogsInRange']);

    // Profile
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/api/profile/modal-data', [ProfileController::class, 'getModalProfileData'])->name('profile.modal.data');
    Route::get('/api/profile/user-data', [ProfileController::class, 'getUserData'])->name('profile.user.data');
    Route::get('/api/profile/weekly-streak', [ProfileController::class, 'getWeeklyStreak'])->name('profile.weekly.streak');
    Route::get('/api/profile/highest-streak', [ProfileController::class, 'getHighestStreak'])->name('profile.highest.streak');
    Route::get('/api/profile/average-calories', [ProfileController::class, 'getAverageCalories'])->name('profile.average.calories');
    Route::get('/api/profile/logs-by-date', [ProfileController::class, 'getLogsGroupedByCategory'])->name('profile.logs.by.date');
    Route::get('/api/profile/logs-range', [ProfileController::class, 'getLogsInRange'])->name('profile.logs.range');
});

