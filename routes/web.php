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

// Settings route
Route::view('/settings', 'settings')->name('settings');
Route::view('/aboutus', 'aboutus')->name('aboutus');
Route::view('/usermanual', 'usermanual')->name('usermanual');

// Notifications route
Route::get('/notifications', function () {
    return view('notifications');
})->name('notifications');

// What Can I Cook route
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

<<<<<<< HEAD
// Profile routes
Route::get('/profile', [ProfileController::class, 'index'])->name('profile');

// Recipe routes
Route::get('/recipes', [RecipeController::class, 'index'])->name('recipes.index');

// 2. The Detail Page (Show) <--- ADD THIS LINE
Route::get('/recipe/{id}', [RecipeController::class, 'show'])->name('recipe.show');

Route::post('/recipe/favorite', [RecipeController::class, 'toggleFavorite'])->name('recipe.toggle');

// Recipe API endpoints (for favorites functionality)
Route::post('/api/recipe/favorite', [RecipeController::class, 'saveFavorite'])->name('recipe.favorite.save');
Route::get('/api/recipe/favorites', [RecipeController::class, 'getFavorites'])->name('recipe.favorites');
Route::delete('/api/recipe/favorite', [RecipeController::class, 'removeFavorite'])->name('recipe.favorite.remove');
=======
// ====== PROFILE PAGE ROUTE ======
Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');

// ====== PROFILE MODAL API ENDPOINTS ======
// These are used by the modal in app.blade.php
Route::get('/api/profile/modal-data', [ProfileController::class, 'getModalProfileData'])->name('profile.modal.data');
Route::post('/api/logout', [ProfileController::class, 'logout'])->name('logout');

// ====== PROFILE PAGE API ENDPOINTS ======
// These are used by profile.blade.php
Route::get('/api/profile/user-data', [ProfileController::class, 'getUserData'])->name('profile.user.data');
Route::get('/api/profile/weekly-streak', [ProfileController::class, 'getWeeklyStreak'])->name('profile.weekly.streak');
Route::get('/api/profile/highest-streak', [ProfileController::class, 'getHighestStreak'])->name('profile.highest.streak');
Route::get('/api/profile/average-calories', [ProfileController::class, 'getAverageCalories'])->name('profile.average.calories');
Route::get('/api/profile/logs-by-date', [ProfileController::class, 'getLogsGroupedByCategory'])->name('profile.logs.by.date');
Route::get('/api/profile/logs-range', [ProfileController::class, 'getLogsInRange'])->name('profile.logs.range');
>>>>>>> 38510453d5c5a128896af4cdb5abe93c6cbd3781
