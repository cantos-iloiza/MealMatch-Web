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
     * Matches Flutter: FirebaseService.getUserData()
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getUserData(Request $request)
    {
        try {
            /* ========================================
             * UNCOMMENT THIS SECTION TO USE REAL FIREBASE DATA
             * ========================================
             * Make sure you have:
             * 1. Firebase Admin SDK installed (composer require kreait/firebase-php)
             * 2. Service account JSON file in storage/firebase/
             * 3. Updated the path to your service account file
             * ========================================
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
            //             'goalCalories' => $userData['goalCalories'] ?? 2000,
            //         ]
            //     ]);
            // } else {
            //     return response()->json([
            //         'success' => false,
            //         'message' => 'User not found'
            //     ], 404);
            // }
            
            /* ========================================
             * END REAL FIREBASE BACKEND
             * ======================================== */
            
            // FAKE DATA - For development/testing only
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => 'Juan Dela Cruz',
                    'email' => 'juandelacruz@email.com',
                    'photoURL' => null,
                    'goalCalories' => 2000
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
     * Matches Flutter: Calculate unique login days in current week
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getWeeklyStreak(Request $request)
    {
        try {
            /* ========================================
             * UNCOMMENT TO USE REAL FIREBASE DATA
             * ======================================== */
            
            // $factory = (new Factory)->withServiceAccount(
            //     storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            // );
            
            // $firestore = $factory->createFirestore();
            // $database = $firestore->database();
            
            // $userId = $request->user_id ?? auth()->id();
            
            // // Calculate start of current week (Sunday)
            // $today = new \DateTime();
            // $dayOfWeek = (int)$today->format('w'); // 0 = Sunday
            // $startOfWeek = clone $today;
            // $startOfWeek->modify("-{$dayOfWeek} days");
            // $startOfWeek->setTime(0, 0, 0);
            
            // // Format dates as YYYY-MM-DD (matches Flutter)
            // $startDateStr = $startOfWeek->format('Y-m-d');
            // $todayStr = $today->format('Y-m-d');
            
            // // Query meal_logs for current week
            // $mealLogsRef = $database->collection('users')->document($userId)->collection('meal_logs');
            // $query = $mealLogsRef
            //     ->where('date', '>=', $startDateStr)
            //     ->where('date', '<=', $todayStr);
            
            // $documents = $query->documents();
            
            // // Count unique dates
            // $uniqueDays = [];
            // foreach ($documents as $doc) {
            //     if ($doc->exists()) {
            //         $data = $doc->data();
            //         $uniqueDays[$data['date']] = true;
            //     }
            // }
            
            // $streakCount = count($uniqueDays);
            
            // return response()->json([
            //     'success' => true,
            //     'data' => [
            //         'weeklyStreak' => $streakCount
            //     ]
            // ]);
            
            /* ========================================
             * END REAL FIREBASE BACKEND
             * ======================================== */
            
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
     * Get highest streak from user stats
     * Note: You may need to calculate this or store it in the user document
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getHighestStreak(Request $request)
    {
        try {
            /* ========================================
             * UNCOMMENT TO USE REAL FIREBASE DATA
             * ======================================== */
            
            // $factory = (new Factory)->withServiceAccount(
            //     storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            // );
            
            // $firestore = $factory->createFirestore();
            // $database = $firestore->database();
            
            // $userId = $request->user_id ?? auth()->id();
            
            // // Fetch user document (assuming highestStreak is stored there)
            // $userRef = $database->collection('users')->document($userId);
            // $userSnapshot = $userRef->snapshot();
            
            // if ($userSnapshot->exists()) {
            //     $userData = $userSnapshot->data();
            //     
            //     return response()->json([
            //         'success' => true,
            //         'data' => [
            //             'highestStreak' => $userData['highestStreak'] ?? 0
            //         ]
            //     ]);
            // }
            
            /* ========================================
             * END REAL FIREBASE BACKEND
             * ======================================== */
            
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
     * Get average daily calorie intake (last 30 days)
     * Matches Flutter: Calculate from meal_logs
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getAverageCalories(Request $request)
    {
        try {
            /* ========================================
             * UNCOMMENT TO USE REAL FIREBASE DATA
             * ======================================== */
            
            // $factory = (new Factory)->withServiceAccount(
            //     storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            // );
            
            // $firestore = $factory->createFirestore();
            // $database = $firestore->database();
            
            // $userId = $request->user_id ?? auth()->id();
            
            // // Calculate date 30 days ago
            // $thirtyDaysAgo = new \DateTime();
            // $thirtyDaysAgo->modify('-30 days');
            // $dateStr = $thirtyDaysAgo->format('Y-m-d');
            
            // // Query meal_logs from last 30 days
            // $mealLogsRef = $database->collection('users')->document($userId)->collection('meal_logs');
            // $query = $mealLogsRef->where('date', '>=', $dateStr);
            
            // $documents = $query->documents();
            
            // $dailyTotals = [];
            // foreach ($documents as $doc) {
            //     if ($doc->exists()) {
            //         $data = $doc->data();
            //         $date = $data['date'];
            //         $calories = $data['calories'] ?? 0;
            //         
            //         if (!isset($dailyTotals[$date])) {
            //             $dailyTotals[$date] = 0;
            //         }
            //         $dailyTotals[$date] += $calories;
            //     }
            // }
            
            // $daysCount = count($dailyTotals);
            // $totalCalories = array_sum($dailyTotals);
            // $avgCalories = $daysCount > 0 ? round($totalCalories / $daysCount) : 0;
            
            // return response()->json([
            //     'success' => true,
            //     'data' => [
            //         'averageCalories' => $avgCalories
            //     ]
            // ]);
            
            /* ========================================
             * END REAL FIREBASE BACKEND
             * ======================================== */
            
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
     * Get logs grouped by category for a specific date
     * Matches Flutter: LogService.getLogsGroupedByCategory(date)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLogsGroupedByCategory(Request $request)
    {
        try {
            $date = $request->input('date'); // YYYY-MM-DD format
            
            if (!$date) {
                return response()->json([
                    'success' => false,
                    'message' => 'Date parameter is required'
                ], 400);
            }
            
            /* ========================================
             * UNCOMMENT TO USE REAL FIREBASE DATA
             * ======================================== */
            
            // $factory = (new Factory)->withServiceAccount(
            //     storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            // );
            
            // $firestore = $factory->createFirestore();
            // $database = $firestore->database();
            
            // $userId = $request->user_id ?? auth()->id();
            
            // // Query meal_logs for specific date
            // $mealLogsRef = $database->collection('users')->document($userId)->collection('meal_logs');
            // $query = $mealLogsRef->where('date', '==', $date);
            
            // $documents = $query->documents();
            
            // $grouped = [
            //     'Breakfast' => [],
            //     'Lunch' => [],
            //     'Dinner' => [],
            //     'Snacks' => []
            // ];
            
            // foreach ($documents as $doc) {
            //     if ($doc->exists()) {
            //         $data = $doc->data();
            //         $log = [
            //             'id' => $doc->id(),
            //             'foodName' => $data['foodName'] ?? 'Unknown Food',
            //             'calories' => $data['calories'] ?? 0,
            //             'carbs' => $data['carbs'] ?? 0,
            //             'proteins' => $data['proteins'] ?? 0,
            //             'fats' => $data['fats'] ?? 0,
            //             'serving' => $data['serving'] ?? '',
            //             'brand' => $data['brand'] ?? '',
            //             'category' => $data['category'] ?? 'Snacks',
            //             'timestamp' => $data['timestamp'] ?? null,
            //             'isVerified' => $data['isVerified'] ?? false,
            //             'source' => $data['source'] ?? ''
            //         ];
            //         
            //         $category = $log['category'];
            //         if (isset($grouped[$category])) {
            //             $grouped[$category][] = $log;
            //         }
            //     }
            // }
            
            // // Sort each category by timestamp (newest first)
            // foreach ($grouped as $category => $logs) {
            //     usort($grouped[$category], function($a, $b) {
            //         return $b['timestamp'] <=> $a['timestamp'];
            //     });
            // }
            
            // return response()->json([
            //     'success' => true,
            //     'data' => $grouped
            // ]);
            
            /* ========================================
             * END REAL FIREBASE BACKEND
             * ======================================== */
            
            // FAKE DATA - Return grouped meal logs for requested date
            $fakeLogs = $this->getFakeMealLogs();
            $grouped = $fakeLogs[$date] ?? [
                'Breakfast' => [],
                'Lunch' => [],
                'Dinner' => [],
                'Snacks' => []
            ];
            
            return response()->json([
                'success' => true,
                'data' => $grouped
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Get logs within a date range
     * Matches Flutter: LogService.getLogsInRange(start, end)
     * 
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getLogsInRange(Request $request)
    {
        try {
            $startDate = $request->input('start_date'); // YYYY-MM-DD
            $endDate = $request->input('end_date'); // YYYY-MM-DD
            
            if (!$startDate || !$endDate) {
                return response()->json([
                    'success' => false,
                    'message' => 'start_date and end_date parameters are required'
                ], 400);
            }
            
            /* ========================================
             * UNCOMMENT TO USE REAL FIREBASE DATA
             * ======================================== */
            
            // $factory = (new Factory)->withServiceAccount(
            //     storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            // );
            
            // $firestore = $factory->createFirestore();
            // $database = $firestore->database();
            
            // $userId = $request->user_id ?? auth()->id();
            
            // // Query meal_logs for date range
            // $mealLogsRef = $database->collection('users')->document($userId)->collection('meal_logs');
            // $query = $mealLogsRef
            //     ->where('date', '>=', $startDate)
            //     ->where('date', '<=', $endDate);
            
            // $documents = $query->documents();
            
            // $logsByDate = [];
            // foreach ($documents as $doc) {
            //     if ($doc->exists()) {
            //         $data = $doc->data();
            //         $date = $data['date'];
            //         
            //         if (!isset($logsByDate[$date])) {
            //             $logsByDate[$date] = [
            //                 'Breakfast' => [],
            //                 'Lunch' => [],
            //                 'Dinner' => [],
            //                 'Snacks' => []
            //             ];
            //         }
            //         
            //         $log = [
            //             'id' => $doc->id(),
            //             'foodName' => $data['foodName'] ?? 'Unknown Food',
            //             'calories' => $data['calories'] ?? 0,
            //             'carbs' => $data['carbs'] ?? 0,
            //             'proteins' => $data['proteins'] ?? 0,
            //             'fats' => $data['fats'] ?? 0,
            //             'serving' => $data['serving'] ?? '',
            //             'brand' => $data['brand'] ?? '',
            //             'category' => $data['category'] ?? 'Snacks',
            //             'timestamp' => $data['timestamp'] ?? null,
            //             'isVerified' => $data['isVerified'] ?? false,
            //             'source' => $data['source'] ?? ''
            //         ];
            //         
            //         $category = $log['category'];
            //         if (isset($logsByDate[$date][$category])) {
            //             $logsByDate[$date][$category][] = $log;
            //         }
            //     }
            // }
            
            // // Sort each category by timestamp
            // foreach ($logsByDate as $date => $grouped) {
            //     foreach ($grouped as $category => $logs) {
            //         usort($logsByDate[$date][$category], function($a, $b) {
            //             return $b['timestamp'] <=> $a['timestamp'];
            //         });
            //     }
            // }
            
            // return response()->json([
            //     'success' => true,
            //     'data' => $logsByDate
            // ]);
            
            /* ========================================
             * END REAL FIREBASE BACKEND
             * ======================================== */
            
            // FAKE DATA - Filter fake logs by date range
            $fakeLogs = $this->getFakeMealLogs();
            $filtered = [];
            
            $start = new \DateTime($startDate);
            $end = new \DateTime($endDate);
            $current = clone $start;
            
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                $filtered[$dateStr] = $fakeLogs[$dateStr] ?? [
                    'Breakfast' => [],
                    'Lunch' => [],
                    'Dinner' => [],
                    'Snacks' => []
                ];
                $current->modify('+1 day');
            }
            
            return response()->json([
                'success' => true,
                'data' => $filtered
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * Calculate total calories from array of logs
     * Matches Flutter: LogService.calculateTotalCalories(logs)
     * 
     * @param array $logs
     * @return float
     */
    private function calculateTotalCalories(array $logs)
    {
        return array_reduce($logs, function($sum, $log) {
            return $sum + ($log['calories'] ?? 0);
        }, 0);
    }
    
    /**
     * FAKE DATA - Mimics Flutter's meal_logs structure
     * This will be replaced when using real Firebase
     */
    private function getFakeMealLogs()
    {
        // Same structure as JavaScript getFakeMealLogs()
        return [
            '2025-12-08' => [
                'Breakfast' => [
                    ['id' => 'log1', 'foodName' => 'Scrambled Eggs', 'calories' => 200, 'carbs' => 2, 'proteins' => 12, 'fats' => 15, 'serving' => '2 eggs', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-08T07:00:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log2', 'foodName' => 'Whole Wheat Toast', 'calories' => 150, 'carbs' => 28, 'proteins' => 6, 'fats' => 2, 'serving' => '2 slices', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-08T07:05:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Lunch' => [
                    ['id' => 'log3', 'foodName' => 'Chicken Salad', 'calories' => 350, 'carbs' => 15, 'proteins' => 30, 'fats' => 18, 'serving' => '1 bowl', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-08T12:00:00', 'isVerified' => false, 'source' => ''],
                    ['id' => 'log4', 'foodName' => 'Rice', 'calories' => 200, 'carbs' => 45, 'proteins' => 4, 'fats' => 0.5, 'serving' => '1 cup', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-08T12:10:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Dinner' => [
                    ['id' => 'log5', 'foodName' => 'Grilled Fish', 'calories' => 400, 'carbs' => 0, 'proteins' => 45, 'fats' => 22, 'serving' => '200g', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-08T18:00:00', 'isVerified' => true, 'source' => 'OFF'],
                    ['id' => 'log6', 'foodName' => 'Vegetables', 'calories' => 100, 'carbs' => 20, 'proteins' => 3, 'fats' => 1, 'serving' => '1 cup', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-08T18:15:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Snacks' => [
                    ['id' => 'log7', 'foodName' => 'Nuts', 'calories' => 200, 'carbs' => 8, 'proteins' => 6, 'fats' => 18, 'serving' => '30g', 'brand' => '', 'category' => 'Snacks', 'timestamp' => '2025-12-08T15:00:00', 'isVerified' => false, 'source' => ''],
                    ['id' => 'log8', 'foodName' => 'Fruit', 'calories' => 250, 'carbs' => 60, 'proteins' => 2, 'fats' => 0.5, 'serving' => '1 apple', 'brand' => '', 'category' => 'Snacks', 'timestamp' => '2025-12-08T20:00:00', 'isVerified' => true, 'source' => 'USDA']
                ]
            ],
            // Add more dates as needed...
            '2025-12-14' => [
                'Breakfast' => [
                    ['id' => 'log42', 'foodName' => 'Oatmeal with Blueberries', 'calories' => 150, 'carbs' => 27, 'proteins' => 5, 'fats' => 3, 'serving' => '1 bowl', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-14T07:00:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log43', 'foodName' => 'Banana', 'calories' => 105, 'carbs' => 27, 'proteins' => 1, 'fats' => 0, 'serving' => '1 medium', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-14T07:10:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log44', 'foodName' => 'Black Coffee', 'calories' => 5, 'carbs' => 0, 'proteins' => 0, 'fats' => 0, 'serving' => '1 cup', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-14T07:15:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Lunch' => [
                    ['id' => 'log45', 'foodName' => 'Grilled Chicken Breast', 'calories' => 284, 'carbs' => 0, 'proteins' => 53, 'fats' => 6, 'serving' => '200g', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-14T12:30:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log46', 'foodName' => 'Brown Rice', 'calories' => 216, 'carbs' => 45, 'proteins' => 5, 'fats' => 2, 'serving' => '1 cup', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-14T12:35:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log47', 'foodName' => 'Steamed Broccoli', 'calories' => 55, 'carbs' => 11, 'proteins' => 4, 'fats' => 0.5, 'serving' => '1 cup', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-14T12:40:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Dinner' => [
                    ['id' => 'log48', 'foodName' => 'Baked Salmon', 'calories' => 367, 'carbs' => 0, 'proteins' => 40, 'fats' => 22, 'serving' => '150g', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-14T19:00:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log49', 'foodName' => 'Roasted Sweet Potato', 'calories' => 112, 'carbs' => 26, 'proteins' => 2, 'fats' => 0, 'serving' => '1 medium', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-14T19:15:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log50', 'foodName' => 'Grilled Asparagus', 'calories' => 40, 'carbs' => 8, 'proteins' => 4, 'fats' => 0, 'serving' => '6 spears', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-14T19:20:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Snacks' => [
                    ['id' => 'log51', 'foodName' => 'Apple', 'calories' => 95, 'carbs' => 25, 'proteins' => 0, 'fats' => 0, 'serving' => '1 medium', 'brand' => '', 'category' => 'Snacks', 'timestamp' => '2025-12-14T15:00:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log52', 'foodName' => 'Almonds (1 oz)', 'calories' => 164, 'carbs' => 6, 'proteins' => 6, 'fats' => 14, 'serving' => '23 almonds', 'brand' => '', 'category' => 'Snacks', 'timestamp' => '2025-12-14T17:00:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log53', 'foodName' => 'Greek Yogurt', 'calories' => 84, 'carbs' => 6, 'proteins' => 15, 'fats' => 0, 'serving' => '170g', 'brand' => '', 'category' => 'Snacks', 'timestamp' => '2025-12-14T20:00:00', 'isVerified' => true, 'source' => 'USDA']
                ]
            ],
            // Include rest of week data...
        ];
    }
}