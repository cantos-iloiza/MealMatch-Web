<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;

class FirebaseServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(Auth::class, function ($app) {
            $factory = (new Factory)->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')));
            return $factory->createAuth();
        });
    }

    public function boot(): void
    {
        //
    }
}