<?php
// config/firebase.php
return [
    'credentials' => [
        'file' => env('FIREBASE_CREDENTIALS', storage_path('storage\app\firebase-credentials.json')),
    ],
    
    'database' => [
        'url' => env('FIREBASE_DATABASE_URL', 'https://mealmatch-web-default-rtdb.firebaseio.com/'),
    ],

    'frontend' => [
        'apiKey' => env('FIREBASE_API_KEY'),
        'authDomain' => env('FIREBASE_AUTH_DOMAIN'),
        'projectId' => env('FIREBASE_PROJECT_ID'),
        'storageBucket' => env('FIREBASE_STORAGE_BUCKET'),
        'messagingSenderId' => env('FIREBASE_MESSAGING_SENDER_ID'),
        'appId' => env('FIREBASE_APP_ID'),
    ],

];
