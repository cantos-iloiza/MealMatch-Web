<?php

// app/Http/Controllers/ModifyFoodController.php
namespace App\Http\Controllers;

use App\Models\MealLog;
use App\Models\User;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;

class ModifyFoodController extends Controller
{
    protected $firebase;

    public function __construct()
    {
        $this->firebase = (new Factory)
            ->withServiceAccount(config('firebase.credentials.file'))
            ->createAuth();
    }

    public function show(Request $request)
    {
        $user = $this->getCurrentUser($request);
        
        if (!$user) {
            return redirect()->route('login');
        }

        $food = $request->session()->get('food_item');
        $preselectedMeal = $request->input('meal') ?? $request->session()->get('selected_meal') ?? 'Breakfast';

        if (!$food) {
            return redirect()->route('food-log.index');
        }

        return view('food-log.modify', compact('food', 'preselectedMeal', 'user'));
    }

    public function setFoodItem(Request $request)
    {
        $request->session()->put('food_item', $request->all());
        return response()->json(['success' => true]);
    }

    public function addFood(Request $request)
    {
        $user = $this->getCurrentUser($request);
        
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'Not authenticated'], 401);
        }

        $validated = $request->validate([
            'category' => 'required|string',
            'food_name' => 'required|string',
            'calories' => 'required|numeric',
            'carbs' => 'required|numeric',
            'fats' => 'required|numeric',
            'proteins' => 'required|numeric',
            'serving' => 'required|string',
            'brand' => 'nullable|string',
        ]);

        $validated['firebase_uid'] = $user->firebase_uid;
        $validated['date'] = today();

        MealLog::create($validated);

        return response()->json([
            'success' => true,
            'message' => "{$validated['food_name']} added to {$validated['category']}!"
        ]);
    }

    protected function getCurrentUser(Request $request)
    {
        $idToken = $request->session()->get('firebase_token') 
                   ?? $request->bearerToken();
        
        if (!$idToken) {
            return null;
        }

        try {
            $verifiedIdToken = $this->firebase->verifyIdToken($idToken);
            $uid = $verifiedIdToken->claims()->get('sub');
            
            return User::where('firebase_uid', $uid)->first();
        } catch (\Exception $e) {
            return null;
        }
    }
}