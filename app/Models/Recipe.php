<?php
// app/Models/Recipe.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    use HasFactory;

    protected $fillable = [
        'firebase_uid',
        'name',
        'description',
        'image',
        'calories',
        'nutrients',
        'ingredients',
        'instructions',
        'prep_time',
        'cook_time',
        'servings',
    ];

    protected $casts = [
        'calories' => 'integer',
        'nutrients' => 'array',
        'ingredients' => 'array',
        'instructions' => 'array',
        'prep_time' => 'integer',
        'cook_time' => 'integer',
        'servings' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'firebase_uid', 'firebase_uid');
    }

    public function mealLogs()
    {
        return $this->hasMany(MealLog::class, 'recipe_id', 'id');
    }

    public function scopeForUser($query, $firebaseUid)
    {
        return $query->where('firebase_uid', $firebaseUid);
    }

    public function getTotalTimeAttribute()
    {
        return ($this->prep_time ?? 0) + ($this->cook_time ?? 0);
    }

    public function getCalculatedCaloriesAttribute()
    {
        if ($this->calories) {
            return $this->calories;
        }

        if (!$this->nutrients) {
            return 0;
        }

        $protein = $this->nutrients['Protein'] ?? $this->nutrients['protein'] ?? 0;
        $carbs = $this->nutrients['Carbs'] ?? $this->nutrients['carbs'] ?? 0;
        $fat = $this->nutrients['Fat'] ?? $this->nutrients['fat'] ?? 0;

        return round(($protein * 4) + ($carbs * 4) + ($fat * 9));
    }

    public function getFormattedTimeAttribute()
    {
        $parts = [];
        
        if ($this->prep_time) {
            $parts[] = "{$this->prep_time} min prep";
        }
        
        if ($this->cook_time) {
            $parts[] = "{$this->cook_time} min cook";
        }

        return !empty($parts) ? implode(' + ', $parts) : 'Time not specified';
    }

    public function getMacrosAttribute()
    {
        if (!$this->nutrients) {
            return null;
        }

        return [
            'protein' => $this->nutrients['Protein'] ?? $this->nutrients['protein'] ?? 0,
            'carbs' => $this->nutrients['Carbs'] ?? $this->nutrients['carbs'] ?? 0,
            'fat' => $this->nutrients['Fat'] ?? $this->nutrients['fat'] ?? 0,
        ];
    }
}