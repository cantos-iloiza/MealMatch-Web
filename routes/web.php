<?php

use Illuminate\Support\Facades\Route;
use Kreait\Firebase\Factory;

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