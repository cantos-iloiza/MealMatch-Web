<?php
// app/Models/MealLog.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MealLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'firebase_uid',
        'category',
        'food_name',
        'brand',
        'calories',
        'carbs',
        'fats',
        'proteins',
        'serving',
        'date',
        'is_verified',
        'source',
        'recipe_id',
    ];

    protected $casts = [
        'calories' => 'decimal:2',
        'carbs' => 'decimal:2',
        'fats' => 'decimal:2',
        'proteins' => 'decimal:2',
        'date' => 'date',
        'is_verified' => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'firebase_uid', 'firebase_uid');
    }

    public function recipe()
    {
        return $this->belongsTo(Recipe::class, 'recipe_id', 'id');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('date', $date);
    }

    public function scopeForUser($query, $firebaseUid)
    {
        return $query->where('firebase_uid', $firebaseUid);
    }

    public function scopeToday($query)
    {
        return $query->whereDate('date', today());
    }

    public function scopeForCategory($query, $category)
    {
        return $query->where('category', $category);
    }

    public function getTotalMacrosAttribute()
    {
        return [
            'calories' => $this->calories,
            'carbs' => $this->carbs,
            'fats' => $this->fats,
            'proteins' => $this->proteins,
        ];
    }
}
