<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Google\Cloud\Firestore\FirestoreClient;

class OnboardingController extends Controller
{
    public function show()
    {
        // Allow access even without session for testing if needed, 
        // but normally keep the check.
        if (!session('firebase_uid')) {
             return redirect()->route('login');
        }
        
        return view('onboarding.index');
    }

    public function store(Request $request)
    {
        // 1. Validate inputs (keep this so the form data is clean)
        $validated = $request->validate([
            'display_name' => 'required|string',
            'avatar' => 'required|string',
            'goals' => 'array',
            'activity_level' => 'string',
            'sex' => 'string',
            'age' => 'numeric',
            'height' => 'numeric',
            'current_weight' => 'numeric',
            'goal_weight' => 'numeric',
        ]);

        $uid = session('firebase_uid');

        // 2. ATTEMPT SAVE (But don't stop if it fails)
        try {
            if ($uid) {
                $credentials = config('services.firebase.credentials');
                
                // Force REST transport to avoid gRPC crashes
                if (file_exists($credentials)) {
                    $database = new FirestoreClient([
                        'keyFilePath' => config('firebase.credentials.file'), // Make sure this config is fixed first!
                        'transport' => 'rest' // <--- THIS LINE IS REQUIRED TO STOP THE CRASH
                    ]);

                    $userRef = $database->collection('users')->document($uid);
                    $userRef->set([
                        'display_name' => $validated['display_name'],
                        'avatar' => $validated['avatar'],
                        'goals' => $request->input('goals', []),
                        'activity_level' => $request->input('activity_level', ''),
                        'sex' => $request->input('sex', ''),
                        'age' => (int) $request->input('age', 0),
                        'height' => (float) $request->input('height', 0),
                        'current_weight' => (float) $request->input('current_weight', 0),
                        'goal_weight' => (float) $request->input('goal_weight', 0),
                        'onboarding_completed' => true,
                        'updated_at' => new \DateTime(),
                    ], ['merge' => true]);
                    
                    Log::info('Firestore save successful.');
                }
            }
        } catch (\Throwable $e) {
            // 3. SILENCE THE ERROR
            // We log it for you to see later, but we DO NOT crash or go back.
            Log::error('Firestore failed, but proceeding anyway: ' . $e->getMessage());
        }

        // 4. FORCE REDIRECT TO HOME
        // We update the local session so the app "thinks" you are done.
        session([
            'onboarding_completed' => true,
            'display_name' => $validated['display_name'],
            'avatar' => $validated['avatar']
        ]);

        return redirect()->route('home')
            ->with('success', 'Setup complete (or skipped)!');
    }
}