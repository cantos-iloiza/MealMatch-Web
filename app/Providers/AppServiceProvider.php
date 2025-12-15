<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\FirebaseService;
use App\Services\CalorieLogService;
use App\Services\RecipeService;
use App\Services\CookedRecipesService;
use App\Services\TheMealDBService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Register Firebase services as singletons
        $this->app->singleton(FirebaseService::class, function ($app) {
            return new FirebaseService();
        });
        
        $this->app->singleton(CalorieLogService::class, function ($app) {
            return new CalorieLogService($app->make(FirebaseService::class));
        });
        
        $this->app->singleton(RecipeService::class, function ($app) {
            return new RecipeService($app->make(FirebaseService::class));
        });
        
        $this->app->singleton(CookedRecipesService::class, function ($app) {
            return new CookedRecipesService($app->make(FirebaseService::class));
        });
        
        $this->app->singleton(TheMealDBService::class, function ($app) {
            return new TheMealDBService();
        });
    }

    public function boot(): void
    {
        //
    }
}
