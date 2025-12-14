<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// Uncomment when Firebase Admin SDK is needed
// use Kreait\Firebase\Factory;
// use Kreait\Firebase\ServiceAccount;

class ProfileController extends Controller
{
    /**
     * Display the user profile page
     * 
     * @return \Illuminate\View\View
     */
    public function index()
    {
        // TODO: Add middleware to check if user is authenticated
        // Example: $this->middleware('auth');
        
        return view('profile');
    }
    
    /**
     * Get user profile data from Firebase
     * This endpoint fetches user data from Firebase Firestore
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserData(Request $request)
    {
        try {
            /* ====== FIREBASE BACKEND - COMMENTED FOR NOW ======
             * Uncomment this section when ready to use Firebase
             * Make sure to update variable names according to your Firebase structure
             */
            
            // $factory = (new Factory)->withServiceAccount(
            //     storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            // );
            
            // $firestore = $factory->createFirestore();
            // $database = $firestore->database();
            
            // // Get current user ID (from session or auth)
            // $userId = $request->user_id ?? auth()->id();
            
            // // Fetch user document from Firestore
            // $userRef = $database->collection('users')->document($userId);
            // $userSnapshot = $userRef->snapshot();
            
            // if ($userSnapshot->exists()) {
            //     $userData = $userSnapshot->data();
            //     
            //     return response()->json([
            //         'success' => true,
            //         'data' => [
            //             'name' => $userData['name'] ?? 'User',
            //             'email' => $userData['email'] ?? 'email@example.com',
            //             'photoURL' => $userData['photoURL'] ?? null,
            //         ]
            //     ]);
            // } else {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'User not found'
            //     ], 404);
            // }
            
            /* ====== END FIREBASE BACKEND ====== */
            
            // FAKE DATA - For development/testing only
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => 'Juan Dela Cruz',
                    'email' => 'juandelacruz@email.com',
                    'photoURL' => null
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get weekly streak data from Firebase
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWeeklyStreak(Request $request)
    {
        try {
            /* ====== FIREBASE BACKEND - COMMENTED FOR NOW ======
             * Uncomment when ready to use Firebase
             */
            
            // $factory = (new Factory)->withServiceAccount(
            //     storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            // );
            
            // $firestore = $factory->createFirestore();
            // $database = $firestore->database();
            
            // $userId = $request->user_id ?? auth()->id();
            
            // // Calculate start of current week (Sunday)
            // $startOfWeek = new \DateTime();
            // $startOfWeek->modify('sunday this week');
            // $startOfWeek->setTime(0, 0, 0);
            
            // // Query login records for current week
            // $loginRecordsRef = $database->collection('loginRecords')
            //     ->where('userId', '=', $userId)
            //     ->where('loginDate', '>=', $startOfWeek);
            
            // $loginRecords = $loginRecordsRef->documents();
            
            // $uniqueDays = [];
            // foreach ($loginRecords as $record) {
            //     $data = $record->data();
            //     $loginDate = $data['loginDate']->toDateTime();
            //     $dayKey = $loginDate->format('Y-m-d');
            //     $uniqueDays[$dayKey] = true;
            // }
            
            // $streakCount = count($uniqueDays);
            
            // return response()->json([
            //     'success' => true,
            //     'data' => [
            //         'weeklyStreak' => $streakCount
            //     ]
            // ]);
            
            /* ====== END FIREBASE BACKEND ====== */
            
            // FAKE DATA
            return response()->json([
                'success' => true,
                'data' => [
                    'weeklyStreak' => 5
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get highest streak from Firebase
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHighestStreak(Request $request)
    {
        try {
            /* ====== FIREBASE BACKEND - COMMENTED FOR NOW ======
             * Uncomment when ready to use Firebase
             */
            
            // $factory = (new Factory)->withServiceAccount(
            //     storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            // );
            
            // $firestore = $factory->createFirestore();
            // $database = $firestore->database();
            
            // $userId = $request->user_id ?? auth()->id();
            
            // // Fetch user stats document
            // $statsRef = $database->collection('userStats')->document($userId);
            // $statsSnapshot = $statsRef->snapshot();
            
            // if ($statsSnapshot->exists()) {
            //     $statsData = $statsSnapshot->data();
            //     
            //     return response()->json([
            //         'success' => true,
            //         'data' => [
            //             'highestStreak' => $statsData['highestStreak'] ?? 0
            //         ]
            //     ]);
            // }
            
            /* ====== END FIREBASE BACKEND ====== */
            
            // FAKE DATA
            return response()->json([
                'success' => true,
                'data' => [
                    'highestStreak' => 21
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get average daily calorie intake
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAverageCalories(Request $request)
    {
        try {
            /* ====== FIREBASE BACKEND - COMMENTED FOR NOW ======
             * Uncomment when ready to use Firebase
             */
            
            // $factory = (new Factory)->withServiceAccount(
            //     storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            // );
            
            // $firestore = $factory->createFirestore();
            // $database = $firestore->database();
            
            // $userId = $request->user_id ?? auth()->id();
            
            // // Calculate date 30 days ago
            // $thirtyDaysAgo = new \DateTime();
            // $thirtyDaysAgo->modify('-30 days');
            // $thirtyDaysAgo->setTime(0, 0, 0);
            
            // // Query food logs from last 30 days
            // $foodLogsRef = $database->collection('foodLogs')
            //     ->where('userId', '=', $userId)
            //     ->where('date', '>=', $thirtyDaysAgo);
            
            // $foodLogs = $foodLogsRef->documents();
            
            // $totalCalories = 0;
            // $daysWithLogs = [];
            
            // foreach ($foodLogs as $log) {
            //     $data = $log->data();
            //     $totalCalories += $data['totalCalories'] ?? 0;
            //     
            //     $logDate = $data['date']->toDateTime();
            //     $dayKey = $logDate->format('Y-m-d');
            //     $daysWithLogs[$dayKey] = true;
            // }
            
            // $avgCalories = count($daysWithLogs) > 0 
            //     ? round($totalCalories / count($daysWithLogs)) 
            //     : 0;
            
            // return response()->json([
            //     'success' => true,
            //     'data' => [
            //         'averageCalories' => $avgCalories
            //     ]
            // ]);
            
            /* ====== END FIREBASE BACKEND ====== */
            
            // FAKE DATA
            return response()->json([
                'success' => true,
                'data' => [
                    'averageCalories' => 1850
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get food logs for today
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTodayLogs(Request $request)
    {
        try {
            /* ====== FIREBASE BACKEND - COMMENTED FOR NOW ======
             * Uncomment when ready to use Firebase
             */
            
            // $factory = (new Factory)->withServiceAccount(
            //     storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            // );
            
            // $firestore = $factory->createFirestore();
            // $database = $firestore->database();
            
            // $userId = $request->user_id ?? auth()->id();
            
            // // Get today's date range
            // $startOfDay = new \DateTime();
            // $startOfDay->setTime(0, 0, 0);
            // 
            // $endOfDay = new \DateTime();
            // $endOfDay->setTime(23, 59, 59);
            
            // // Query food logs for today
            // $foodLogsRef = $database->collection('foodLogs')
            //     ->where('userId', '=', $userId)
            //     ->where('date', '>=', $startOfDay)
            //     ->where('date', '<=', $endOfDay)
            //     ->orderBy('date', 'DESC');
            
            // $foodLogs = $foodLogsRef->documents();
            
            // $logs = [];
            // foreach ($foodLogs as $log) {
            //     $data = $log->data();
            //     $logs[] = [
            //         'id' => $log->id(),
            //         'mealType' => $data['mealType'] ?? 'Meal',
            //         'foods' => $data['foods'] ?? [],
            //         'totalCalories' => $data['totalCalories'] ?? 0,
            //         'date' => $data['date']->toDateTime()->format('Y-m-d H:i:s')
            //     ];
            // }
            
            // return response()->json([
            //     'success' => true,
            //     'data' => $logs
            // ]);
            
            /* ====== END FIREBASE BACKEND ====== */
            
            // FAKE DATA
            return response()->json([
                'success' => true,
                'data' => [
                    [
                        'mealType' => 'Breakfast',
                        'totalCalories' => 260,
                        'foods' => [
                            ['name' => 'Oatmeal with Blueberries', 'calories' => 150],
                            ['name' => 'Banana', 'calories' => 105],
                            ['name' => 'Black Coffee', 'calories' => 5]
                        ]
                    ],
                    [
                        'mealType' => 'Lunch',
                        'totalCalories' => 555,
                        'foods' => [
                            ['name' => 'Grilled Chicken Breast', 'calories' => 284],
                            ['name' => 'Brown Rice', 'calories' => 216],
                            ['name' => 'Steamed Broccoli', 'calories' => 55]
                        ]
                    ],
                    [
                        'mealType' => 'Dinner',
                        'totalCalories' => 639,
                        'foods' => [
                            ['name' => 'Baked Salmon', 'calories' => 367],
                            ['name' => 'Roasted Sweet Potato', 'calories' => 112],
                            ['name' => 'Grilled Asparagus', 'calories' => 40],
                            ['name' => 'Green Salad', 'calories' => 120]
                        ]
                    ],
                    [
                        'mealType' => 'Snacks',
                        'totalCalories' => 343,
                        'foods' => [
                            ['name' => 'Apple', 'calories' => 95],
                            ['name' => 'Almonds (1 oz)', 'calories' => 164],
                            ['name' => 'Greek Yogurt', 'calories' => 84]
                        ]
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get food logs for current week
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWeekLogs(Request $request)
    {
        try {
            /* ====== FIREBASE BACKEND - COMMENTED FOR NOW ======
             * Uncomment when ready to use Firebase
             */
            
            // Similar structure to getTodayLogs but query for the whole week
            // $startOfWeek = new \DateTime();
            // $startOfWeek->modify('sunday this week');
            // ... etc
            
            /* ====== END FIREBASE BACKEND ====== */
            
            // FAKE DATA - Returns weekly overview
            return response()->json([
                'success' => true,
                'data' => [
                    [
                        'date' => '2025-12-08',
                        'dayName' => 'Monday',
                        'goal' => 2000,
                        'logged' => 1850,
                        'remaining' => 150,
                        'status' => 'On Track'
                    ],
                    [
                        'date' => '2025-12-09',
                        'dayName' => 'Tuesday',
                        'goal' => 2000,
                        'logged' => 2100,
                        'remaining' => -100,
                        'status' => 'Over Goal'
                    ],
                    [
                        'date' => '2025-12-10',
                        'dayName' => 'Wednesday',
                        'goal' => 2000,
                        'logged' => 1920,
                        'remaining' => 80,
                        'status' => 'On Track'
                    ],
                    [
                        'date' => '2025-12-11',
                        'dayName' => 'Thursday',
                        'goal' => 2000,
                        'logged' => 1780,
                        'remaining' => 220,
                        'status' => 'On Track'
                    ],
                    [
                        'date' => '2025-12-12',
                        'dayName' => 'Friday',
                        'goal' => 2000,
                        'logged' => 1950,
                        'remaining' => 50,
                        'status' => 'On Track'
                    ],
                    [
                        'date' => '2025-12-13',
                        'dayName' => 'Saturday',
                        'goal' => 2000,
                        'logged' => 0,
                        'remaining' => 2000,
                        'status' => 'No Logs'
                    ],
                    [
                        'date' => '2025-12-14',
                        'dayName' => 'Sunday',
                        'goal' => 2000,
                        'logged' => 1597,
                        'remaining' => 403,
                        'status' => 'On Track'
                    ]
                ]
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
}