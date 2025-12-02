<?php

use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Factory;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/test-firebase', function () {
    try {
        $factory = (new \Kreait\Firebase\Factory)
            ->withServiceAccount(storage_path('firebase/mealmatch-web-credentials.json'));
        
        $auth = $factory->createAuth();
        
        // Test if connection works
        $users = $auth->listUsers($maxResults = 1);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Firebase connection is working!',
            'firebase_connected' => true
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error',
            'message' => $e->getMessage()
        ], 500);
    }
});