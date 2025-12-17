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
     */
    public function index()
    {
        // TODO: Add middleware to check if user is authenticated
        // Example: $this->middleware('auth');
        
        return view('profile');
    }
    
    /**
     * Get logs within a date range
     * Matches Flutter: LogService.getLogsInRange(start, end)
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
            
            // Validate date format
            $start = \DateTime::createFromFormat('Y-m-d', $startDate);
            $end = \DateTime::createFromFormat('Y-m-d', $endDate);
            
            if (!$start || !$end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid date format. Use YYYY-MM-DD'
                ], 400);
            }
            
            if ($start > $end) {
                return response()->json([
                    'success' => false,
                    'message' => 'Start date must be before or equal to end date'
                ], 400);
            }
            
            /* ========================================
             * UNCOMMENT TO USE REAL FIREBASE DATA
             * ========================================
             
            $factory = (new Factory)->withServiceAccount(
                storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            );
            
            $firestore = $factory->createFirestore();
            $database = $firestore->database();
            
            $userId = $request->user_id ?? auth()->id();
            
            if (!$userId) {
                return response()->json([
                    'success' => false,
                    'message' => 'User not authenticated'
                ], 401);
            }
            
            // Query meal_logs between dates
            $mealLogsRef = $database->collection('users')->document($userId)->collection('meal_logs');
            $query = $mealLogsRef
                ->where('date', '>=', $startDate)
                ->where('date', '<=', $endDate)
                ->orderBy('date')
                ->orderBy('timestamp');
            
            $documents = $query->documents();
            
            // Group logs by date and category
            $filtered = [];
            
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $data = $doc->data();
                    $date = $data['date'] ?? null;
                    $category = $data['category'] ?? 'Snacks';
                    
                    if (!$date) continue;
                    
                    if (!isset($filtered[$date])) {
                        $filtered[$date] = [
                            'Breakfast' => [],
                            'Lunch' => [],
                            'Dinner' => [],
                            'Snacks' => []
                        ];
                    }
                    
                    $log = [
                        'id' => $doc->id(),
                        'foodName' => $data['foodName'] ?? 'Unknown Food',
                        'calories' => $data['calories'] ?? 0,
                        'carbs' => $data['carbs'] ?? 0,
                        'proteins' => $data['proteins'] ?? 0,
                        'fats' => $data['fats'] ?? 0,
                        'serving' => $data['serving'] ?? '',
                        'brand' => $data['brand'] ?? '',
                        'category' => $category,
                        'timestamp' => $data['timestamp'] ?? null,
                        'isVerified' => $data['isVerified'] ?? false,
                        'source' => $data['source'] ?? ''
                    ];
                    
                    if (isset($filtered[$date][$category])) {
                        $filtered[$date][$category][] = $log;
                    }
                }
            }
            
            // Fill in missing dates with empty arrays
            $current = clone $start;
            while ($current <= $end) {
                $dateStr = $current->format('Y-m-d');
                if (!isset($filtered[$dateStr])) {
                    $filtered[$dateStr] = [
                        'Breakfast' => [],
                        'Lunch' => [],
                        'Dinner' => [],
                        'Snacks' => []
                    ];
                }
                $current->modify('+1 day');
            }
            
            // Sort by date
            ksort($filtered);
            
            return response()->json([
                'success' => true,
                'data' => $filtered
            ]);
            
            ========================================
            END REAL FIREBASE BACKEND
            ======================================== */
            
            // FAKE DATA - Filter fake logs by date range
            $fakeLogs = $this->getFakeMealLogs();
            $filtered = [];

            $currentDate = clone $start;

            while ($currentDate <= $end) {
                $dateStr = $currentDate->format('Y-m-d');
                $filtered[$dateStr] = $fakeLogs[$dateStr] ?? [
                    'Breakfast' => [],
                    'Lunch' => [],
                    'Dinner' => [],
                    'Snacks' => []
                ];
                $currentDate->add(new \DateInterval('P1D')); // Add 1 day properly
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
     * Get logs grouped by category for a specific date
     * Matches Flutter: LogService.getLogsGroupedByCategory(date)
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
             * ========================================
             
            $factory = (new Factory)->withServiceAccount(
                storage_path('firebase/mealmatch-web-firebase-adminsdk-fbsvc-7cc8a28f53.json')
            );
            
            $firestore = $factory->createFirestore();
            $database = $firestore->database();
            
            $userId = $request->user_id ?? auth()->id();
            
            // Query meal_logs for specific date
            $mealLogsRef = $database->collection('users')->document($userId)->collection('meal_logs');
            $query = $mealLogsRef->where('date', '==', $date);
            
            $documents = $query->documents();
            
            $grouped = [
                'Breakfast' => [],
                'Lunch' => [],
                'Dinner' => [],
                'Snacks' => []
            ];
            
            foreach ($documents as $doc) {
                if ($doc->exists()) {
                    $data = $doc->data();
                    $log = [
                        'id' => $doc->id(),
                        'foodName' => $data['foodName'] ?? 'Unknown Food',
                        'calories' => $data['calories'] ?? 0,
                        'carbs' => $data['carbs'] ?? 0,
                        'proteins' => $data['proteins'] ?? 0,
                        'fats' => $data['fats'] ?? 0,
                        'serving' => $data['serving'] ?? '',
                        'brand' => $data['brand'] ?? '',
                        'category' => $data['category'] ?? 'Snacks',
                        'timestamp' => $data['timestamp'] ?? null,
                        'isVerified' => $data['isVerified'] ?? false,
                        'source' => $data['source'] ?? ''
                    ];
                    
                    $category = $log['category'];
                    if (isset($grouped[$category])) {
                        $grouped[$category][] = $log;
                    }
                }
            }
            
            // Sort each category by timestamp (newest first)
            foreach ($grouped as $category => $logs) {
                usort($grouped[$category], function($a, $b) {
                    return $b['timestamp'] <=> $a['timestamp'];
                });
            }
            
            return response()->json([
                'success' => true,
                'data' => $grouped
            ]);
            
            ========================================
            END REAL FIREBASE BACKEND
            ======================================== */
            
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
     * Get user profile data from Firebase
     * Matches Flutter: FirebaseService.getUserData()
     */
    public function getUserData(Request $request)
    {
        try {
            /* UNCOMMENT FOR REAL FIREBASE */
            
            // FAKE DATA
            return response()->json([
                'success' => true,
                'data' => [
                    'name' => 'Hermione',
                    'email' => 'mealmatch03@email.com',
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
     */
    public function getWeeklyStreak(Request $request)
    {
        try {
            // FAKE DATA
            return response()->json([
                'success' => true,
                'data' => [
                    'weeklyStreak' => 2
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
     */
    public function getHighestStreak(Request $request)
    {
        try {
            // FAKE DATA
            return response()->json([
                'success' => true,
                'data' => [
                    'highestStreak' => 2
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
     */
    public function getAverageCalories(Request $request)
    {
        try {
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
     * Handle user logout
     */
    public function logout(Request $request)
    {
        try {
            // FAKE LOGOUT - For development
            $request->session()->flush();
            
            return response()->json([
                'success' => true,
                'message' => 'Logged out successfully'
            ]);
            
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 500);
        }
    }
    
    /**
     * FAKE DATA - Mimics Flutter's meal_logs structure
     * This will be replaced when using real Firebase
     */
    private function getFakeMealLogs()
    {
        return [
            '2025-12-14' => [
                'Breakfast' => [
                    ['id' => 'log1', 'foodName' => 'Oatmeal with Blueberries', 'calories' => 150, 'carbs' => 27, 'proteins' => 5, 'fats' => 3, 'serving' => '1 bowl', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-14T07:00:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log2', 'foodName' => 'Banana', 'calories' => 105, 'carbs' => 27, 'proteins' => 1, 'fats' => 0, 'serving' => '1 medium', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-14T07:10:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log3', 'foodName' => 'Black Coffee', 'calories' => 5, 'carbs' => 0, 'proteins' => 0, 'fats' => 0, 'serving' => '1 cup', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-14T07:15:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Lunch' => [
                    ['id' => 'log4', 'foodName' => 'Grilled Chicken Breast', 'calories' => 284, 'carbs' => 0, 'proteins' => 53, 'fats' => 6, 'serving' => '200g', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-14T12:30:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log5', 'foodName' => 'Brown Rice', 'calories' => 216, 'carbs' => 45, 'proteins' => 5, 'fats' => 2, 'serving' => '1 cup', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-14T12:35:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log6', 'foodName' => 'Steamed Broccoli', 'calories' => 55, 'carbs' => 11, 'proteins' => 4, 'fats' => 0.5, 'serving' => '1 cup', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-14T12:40:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Dinner' => [
                    ['id' => 'log7', 'foodName' => 'Baked Salmon', 'calories' => 367, 'carbs' => 0, 'proteins' => 40, 'fats' => 22, 'serving' => '150g', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-14T19:00:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log8', 'foodName' => 'Roasted Sweet Potato', 'calories' => 112, 'carbs' => 26, 'proteins' => 2, 'fats' => 0, 'serving' => '1 medium', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-14T19:15:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log9', 'foodName' => 'Grilled Asparagus', 'calories' => 40, 'carbs' => 8, 'proteins' => 4, 'fats' => 0, 'serving' => '6 spears', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-14T19:20:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Snacks' => [
                    ['id' => 'log10', 'foodName' => 'Apple', 'calories' => 95, 'carbs' => 25, 'proteins' => 0, 'fats' => 0, 'serving' => '1 medium', 'brand' => '', 'category' => 'Snacks', 'timestamp' => '2025-12-14T15:00:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log11', 'foodName' => 'Almonds (1 oz)', 'calories' => 164, 'carbs' => 6, 'proteins' => 6, 'fats' => 14, 'serving' => '23 almonds', 'brand' => '', 'category' => 'Snacks', 'timestamp' => '2025-12-14T17:00:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log12', 'foodName' => 'Greek Yogurt', 'calories' => 84, 'carbs' => 6, 'proteins' => 15, 'fats' => 0, 'serving' => '170g', 'brand' => '', 'category' => 'Snacks', 'timestamp' => '2025-12-14T20:00:00', 'isVerified' => true, 'source' => 'USDA']
                ]
            ],
            '2025-12-15' => [
                'Breakfast' => [
                    ['id' => 'log13', 'foodName' => 'Scrambled Eggs', 'calories' => 200, 'carbs' => 2, 'proteins' => 12, 'fats' => 15, 'serving' => '2 eggs', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-15T07:00:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log14', 'foodName' => 'Whole Wheat Toast', 'calories' => 150, 'carbs' => 28, 'proteins' => 6, 'fats' => 2, 'serving' => '2 slices', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-15T07:05:00', 'isVerified' => true, 'source' => 'USDA'],
                    ['id' => 'log15', 'foodName' => 'Orange Juice', 'calories' => 110, 'carbs' => 26, 'proteins' => 2, 'fats' => 0, 'serving' => '1 cup', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-15T07:10:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Lunch' => [
                    ['id' => 'log16', 'foodName' => 'Chicken Salad', 'calories' => 350, 'carbs' => 15, 'proteins' => 30, 'fats' => 18, 'serving' => '1 bowl', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-15T12:00:00', 'isVerified' => false, 'source' => ''],
                    ['id' => 'log17', 'foodName' => 'Rice', 'calories' => 200, 'carbs' => 45, 'proteins' => 4, 'fats' => 0.5, 'serving' => '1 cup', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-15T12:10:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Dinner' => [
                    ['id' => 'log18', 'foodName' => 'Grilled Fish', 'calories' => 400, 'carbs' => 0, 'proteins' => 45, 'fats' => 22, 'serving' => '200g', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-15T18:00:00', 'isVerified' => true, 'source' => 'OFF'],
                    ['id' => 'log19', 'foodName' => 'Vegetables', 'calories' => 100, 'carbs' => 20, 'proteins' => 3, 'fats' => 1, 'serving' => '1 cup', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-15T18:15:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Snacks' => [
                    ['id' => 'log20', 'foodName' => 'Nuts', 'calories' => 200, 'carbs' => 8, 'proteins' => 6, 'fats' => 18, 'serving' => '30g', 'brand' => '', 'category' => 'Snacks', 'timestamp' => '2025-12-15T15:00:00', 'isVerified' => false, 'source' => ''],
                    ['id' => 'log21', 'foodName' => 'Fruit', 'calories' => 250, 'carbs' => 60, 'proteins' => 2, 'fats' => 0.5, 'serving' => '1 apple', 'brand' => '', 'category' => 'Snacks', 'timestamp' => '2025-12-15T20:00:00', 'isVerified' => true, 'source' => 'USDA']
                ]
            ],
            '2025-12-10' => [
                'Breakfast' => [
                    ['id' => 'log22', 'foodName' => 'Pancakes', 'calories' => 350, 'carbs' => 60, 'proteins' => 8, 'fats' => 10, 'serving' => '3 pancakes', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-12-10T08:00:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Lunch' => [
                    ['id' => 'log23', 'foodName' => 'Burger', 'calories' => 550, 'carbs' => 45, 'proteins' => 30, 'fats' => 25, 'serving' => '1 burger', 'brand' => '', 'category' => 'Lunch', 'timestamp' => '2025-12-10T13:00:00', 'isVerified' => false, 'source' => '']
                ],
                'Dinner' => [
                    ['id' => 'log24', 'foodName' => 'Pizza', 'calories' => 600, 'carbs' => 70, 'proteins' => 25, 'fats' => 22, 'serving' => '3 slices', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-12-10T19:00:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Snacks' => []
            ],
            '2025-11-28' => [
                'Breakfast' => [
                    ['id' => 'log25', 'foodName' => 'Cereal', 'calories' => 200, 'carbs' => 42, 'proteins' => 4, 'fats' => 2, 'serving' => '1 bowl', 'brand' => '', 'category' => 'Breakfast', 'timestamp' => '2025-11-28T07:30:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Lunch' => [],
                'Dinner' => [
                    ['id' => 'log26', 'foodName' => 'Pasta', 'calories' => 450, 'carbs' => 80, 'proteins' => 15, 'fats' => 8, 'serving' => '1 plate', 'brand' => '', 'category' => 'Dinner', 'timestamp' => '2025-11-28T19:00:00', 'isVerified' => true, 'source' => 'USDA']
                ],
                'Snacks' => []
            ]
        ];
    }
}