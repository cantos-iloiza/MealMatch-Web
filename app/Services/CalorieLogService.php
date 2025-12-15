<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Collection;
use Carbon\Carbon;

class CalorieLogService
{
    /**
     * Get today's meal logs for a user
     *
     * @param User $user
     * @return Collection
     */
    public function getTodayLogs(User $user): Collection
    {
        return $this->getLogsByDate($user, Carbon::today());
    }

    /**
     * Get meal logs for a specific date
     *
     * @param User $user
     * @param Carbon $date
     * @return Collection
     */
    public function getLogsByDate(User $user, Carbon $date): Collection
    {
        $dateStr = $date->format('Y-m-d');
        
        return DB::connection('firebase')
            ->collection('users')
            ->document($user->firebase_uid)
            ->collection('meal_logs')
            ->where('date', '=', $dateStr)
            ->orderBy('timestamp', 'desc')
            ->get();
    }

    /**
     * Get logs by date and meal category
     *
     * @param User $user
     * @param Carbon $date
     * @param string $category (Breakfast, Lunch, Dinner, Snacks)
     * @return Collection
     */
    public function getLogsByDateAndCategory(User $user, Carbon $date, string $category): Collection
    {
        $dateStr = $date->format('Y-m-d');
        
        return DB::connection('firebase')
            ->collection('users')
            ->document($user->firebase_uid)
            ->collection('meal_logs')
            ->where('date', '=', $dateStr)
            ->where('category', '=', $category)
            ->orderBy('timestamp', 'desc')
            ->get();
    }

    /**
     * Get logs within a date range
     *
     * @param User $user
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return Collection
     */
    public function getLogsInRange(User $user, Carbon $startDate, Carbon $endDate): Collection
    {
        $dateStrings = $this->generateDateRange($startDate, $endDate);
        
        // Firestore has a limit of 10 items in 'whereIn' queries
        if (count($dateStrings) <= 10) {
            return DB::connection('firebase')
                ->collection('users')
                ->document($user->firebase_uid)
                ->collection('meal_logs')
                ->whereIn('date', $dateStrings)
                ->orderBy('timestamp', 'desc')
                ->get();
        }
        
        // For ranges > 10 days, fetch in batches
        return $this->fetchLogsInBatches($user, $dateStrings);
    }

    /**
     * Calculate total calories from meal logs
     *
     * @param Collection $logs
     * @return float
     */
    public function calculateTotalCalories(Collection $logs): float
    {
        return $logs->sum('calories');
    }

    /**
     * Calculate total macros from meal logs
     *
     * @param Collection $logs
     * @return array
     */
    public function calculateTotalMacros(Collection $logs): array
    {
        return [
            'carbs' => $logs->sum('carbs'),
            'proteins' => $logs->sum('proteins'),
            'fats' => $logs->sum('fats'),
        ];
    }

    /**
     * Get today's summary with goals
     *
     * @param User $user
     * @return array
     */
    public function getTodaySummary(User $user): array
    {
        $logs = $this->getTodayLogs($user);
        $totalCalories = $this->calculateTotalCalories($logs);
        $macros = $this->calculateTotalMacros($logs);
        
        $goalCalories = $user->daily_calorie_goal ?? 2000;
        $remaining = $goalCalories - $totalCalories;

        return [
            'total_calories' => $totalCalories,
            'goal_calories' => $goalCalories,
            'remaining_calories' => $remaining,
            'total_carbs' => $macros['carbs'],
            'total_proteins' => $macros['proteins'],
            'total_fats' => $macros['fats'],
            'logs_count' => $logs->count(),
        ];
    }

    /**
     * Group logs by meal category
     *
     * @param Collection $logs
     * @return array
     */
    public function groupLogsByCategory(Collection $logs): array
    {
        return $logs->groupBy('category')->map(function ($categoryLogs) {
            return [
                'logs' => $categoryLogs,
                'total_calories' => $categoryLogs->sum('calories'),
            ];
        })->toArray();
    }

    /**
     * Delete a meal log
     *
     * @param User $user
     * @param string $logId
     * @return bool
     */
    public function deleteMealLog(User $user, string $logId): bool
    {
        try {
            DB::connection('firebase')
                ->collection('users')
                ->document($user->firebase_uid)
                ->collection('meal_logs')
                ->document($logId)
                ->delete();
                
            return true;
        } catch (\Exception $e) {
            \Log::error('Failed to delete meal log: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Add a new meal log
     *
     * @param User $user
     * @param array $data
     * @return string|null Document ID
     */
    public function addMealLog(User $user, array $data): ?string
    {
        try {
            $logData = [
                'user_id' => $user->firebase_uid,
                'category' => $data['category'],
                'food_name' => $data['food_name'],
                'calories' => $data['calories'],
                'carbs' => $data['carbs'] ?? 0,
                'fats' => $data['fats'] ?? 0,
                'proteins' => $data['proteins'] ?? 0,
                'serving' => $data['serving'] ?? '1 serving',
                'timestamp' => now(),
                'date' => Carbon::parse($data['date'] ?? now())->format('Y-m-d'),
                'brand' => $data['brand'] ?? '',
                'is_verified' => $data['is_verified'] ?? false,
                'source' => $data['source'] ?? 'Local',
            ];

            $docRef = DB::connection('firebase')
                ->collection('users')
                ->document($user->firebase_uid)
                ->collection('meal_logs')
                ->add($logData);
                
            return $docRef->id();
        } catch (\Exception $e) {
            \Log::error('Failed to add meal log: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Calculate meal logging streak
     *
     * @param User $user
     * @return int Days of consecutive logging
     */
    public function calculateStreak(User $user): int
    {
        $streak = 0;
        $currentDate = Carbon::today();
        
        for ($i = 0; $i < 365; $i++) {
            $logs = $this->getLogsByDate($user, $currentDate);
            
            if ($logs->isEmpty()) {
                break;
            }
            
            $streak++;
            $currentDate->subDay();
        }
        
        return $streak;
    }

    /**
     * Get weekly calorie summary
     *
     * @param User $user
     * @return array
     */
    public function getWeeklySummary(User $user): array
    {
        $startDate = Carbon::today()->subDays(6);
        $endDate = Carbon::today();
        
        $logs = $this->getLogsInRange($user, $startDate, $endDate);
        
        $dailySummary = [];
        for ($date = $startDate->copy(); $date <= $endDate; $date->addDay()) {
            $dateStr = $date->format('Y-m-d');
            $dayLogs = $logs->where('date', $dateStr);
            
            $dailySummary[] = [
                'date' => $dateStr,
                'day_name' => $date->format('D'),
                'total_calories' => $dayLogs->sum('calories'),
                'meals_logged' => $dayLogs->count(),
            ];
        }
        
        return [
            'period' => [
                'start' => $startDate->format('Y-m-d'),
                'end' => $endDate->format('Y-m-d'),
            ],
            'daily_summary' => $dailySummary,
            'weekly_total' => $logs->sum('calories'),
            'weekly_average' => round($logs->sum('calories') / 7, 2),
        ];
    }

    /**
     * Generate array of date strings between two dates
     *
     * @param Carbon $startDate
     * @param Carbon $endDate
     * @return array
     */
    private function generateDateRange(Carbon $startDate, Carbon $endDate): array
    {
        $dates = [];
        $current = $startDate->copy();
        
        while ($current <= $endDate) {
            $dates[] = $current->format('Y-m-d');
            $current->addDay();
        }
        
        return $dates;
    }

    /**
     * Fetch logs in batches for large date ranges
     *
     * @param User $user
     * @param array $dateStrings
     * @return Collection
     */
    private function fetchLogsInBatches(User $user, array $dateStrings): Collection
    {
        $allLogs = collect();
        $batches = array_chunk($dateStrings, 10);
        
        foreach ($batches as $batch) {
            $batchLogs = DB::connection('firebase')
                ->collection('users')
                ->document($user->firebase_uid)
                ->collection('meal_logs')
                ->whereIn('date', $batch)
                ->get();
                
            $allLogs = $allLogs->merge($batchLogs);
        }
        
        return $allLogs->sortByDesc('timestamp');
    }
}