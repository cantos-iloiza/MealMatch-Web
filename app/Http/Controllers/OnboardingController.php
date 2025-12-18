<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Services\FirebaseService; // Import the service

class OnboardingController extends Controller
{
    protected $firebaseService;

    // Inject the service
    public function __construct(FirebaseService $firebaseService)
    {
        $this->firebaseService = $firebaseService;
    }

    public function show()
    {
        if (!session('firebase_uid')) {
             return redirect()->route('login');
        }
        
        return view('onboarding.index');
    }

    public function store(Request $request)
    {
        // 1. Validate inputs
        $validated = $request->validate([
            'display_name' => 'required|string',
            'avatar' => 'required|string',
            'goals' => 'array',         // Ensure this is an array
            'activity_level' => 'string',
            'sex' => 'string',
            'age' => 'numeric',
            'height' => 'numeric',
            'current_weight' => 'numeric',
            'goal_weight' => 'numeric',
        ]);

        $uid = session('firebase_uid');

        if (!$uid) {
            return redirect()->route('login')->with('error', 'Session expired. Please log in again.');
        }

        // 2. Prepare Data for Firestore
        // We calculate a preliminary calorie goal here so it's ready for the home page
        $calorieGoal = $this->calculateCalorieGoal(
            $request->input('sex'),
            $request->input('current_weight'),
            $request->input('height'),
            $request->input('age'),
            $request->input('activity_level'),
            $request->input('goals', [])
        );

        $userProfile = [
            'display_name' => $validated['display_name'],
            'avatar' => $validated['avatar'],
            'goals' => $request->input('goals', []), // Explicitly get the array
            'activity_level' => $request->input('activity_level', ''),
            'sex' => $request->input('sex', ''),
            'age' => (int) $request->input('age', 0),
            'height' => (float) $request->input('height', 0),
            'current_weight' => (float) $request->input('current_weight', 0),
            'goal_weight' => (float) $request->input('goal_weight', 0),
            'calorieGoal' => $calorieGoal,
            'onboarding_completed' => true,
            'updated_at' => new \DateTime(),
        ];

        // 3. Save to Firestore using FirebaseService
        try {
            // 'users' is the collection, $uid is the document ID
            $this->firebaseService->updateDocument('users', $uid, $userProfile);
            Log::info("Onboarding data saved for user: $uid");

        } catch (\Throwable $e) {
            Log::error('Firestore Error: ' . $e->getMessage());
        }

        // 4. Update Session and Redirect
        session([
            'onboarding_completed' => true,
            'display_name' => $validated['display_name'],
            'avatar' => $validated['avatar']
        ]);

        return redirect()->route('home')->with('success', 'Profile Setup Complete!');
    }

    /**
     * Helper to calculate calorie goal based on Mifflin-St Jeor Equation
     */
    private function calculateCalorieGoal($sex, $weight, $height, $age, $activityLevel, $goals)
    {
        // 1. Calculate BMR
        if ($sex === 'male') {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) + 5;
        } else {
            $bmr = (10 * $weight) + (6.25 * $height) - (5 * $age) - 161;
        }

        // 2. Activity Multiplier
        $multipliers = [
            'sedentary' => 1.2,
            'lightly_active' => 1.375,
            'active' => 1.55,
            'very_active' => 1.9
        ];
        $tdee = $bmr * ($multipliers[$activityLevel] ?? 1.2);

        // 3. Goal Adjustment
        if (in_array('lose_weight', $goals)) {
            return round($tdee - 500);
        } elseif (in_array('gain_weight', $goals)) {
            return round($tdee + 500);
        }

        return round($tdee);
    }
}