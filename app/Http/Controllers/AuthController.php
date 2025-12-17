<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Exception\Auth\EmailExists;

class AuthController extends Controller
{
    // --- REGISTRATION METHOD ---
    public function store(Request $request)
    {
        $request->validate([
            'display_name' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ]);

        try {
            // Use the config helper to get the path you fixed in config/services.php
            $factory = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials'));
            
            $auth = $factory->createAuth();

            $userProperties = [
                'email' => $request->email,
                'emailVerified' => false,
                'password' => $request->password,
                'displayName' => $request->display_name,
            ];

            $createdUser = $auth->createUser($userProperties);

            return redirect()->route('login')
                ->with('success', 'Account created successfully! Please sign in.');

        } catch (EmailExists $e) {
            return back()->withErrors(['email' => 'This email address is already in use.']);
        } catch (\Throwable $e) {
            Log::error('Register Error: ' . $e->getMessage());
            return back()->withErrors(['error' => 'Registration failed: ' . $e->getMessage()]);
        }
    }

    // --- LOGIN METHOD (The one causing your crash) ---
    public function sessionLogin(Request $request)
    {
        $request->validate([
            'idToken' => 'required|string',
        ]);

        try {
            // 1. Initialize Firebase with the correct credential path
            // We use the same config key 'services.firebase.credentials' that you fixed.
            $factory = (new Factory)
                ->withServiceAccount(config('services.firebase.credentials'));

            $auth = $factory->createAuth();

            // 2. Verify the Google Token
            $verified = $auth->verifyIdToken($request->idToken);
            $uid = $verified->claims()->get('sub');

            // 3. Store in Session
            session(['firebase_uid' => $uid]);

            Log::info('Login successful for UID: ' . $uid);

            return response()->json([
                'status' => 'success',
                'redirect_url' => route('onboarding.index') 
            ]);

        } catch (\Throwable $e) {
            Log::error('Login Error: ' . $e->getMessage());
            
            // Return a JSON error so the frontend knows what happened
            return response()->json([
                'status' => 'error',
                'message' => 'Login failed: ' . $e->getMessage()
            ], 401);
        }
    }

    public function logout(Request $request)
    {
        $request->session()->flush();
        return redirect('/');
    }
}