<?php
// app/Models/Food.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'calories',
        'carbs',
        'protein',
        'fat',
        'serving_size',
        'serving_options',
        'is_verified',
        'source',
        'usda_fdc_id',
        'barcode',
    ];

    protected $casts = [
        'calories' => 'decimal:2',
        'carbs' => 'decimal:2',
        'protein' => 'decimal:2',
        'fat' => 'decimal:2',
        'serving_options' => 'array',
        'is_verified' => 'boolean',
    ];

    public function scopeVerified($query)
    {
        return $query->where('is_verified', true);
    }

    public function scopeSearch($query, $searchTerm)
    {
        return $query->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('brand', 'like', "%{$searchTerm}%");
    }

    public function scopeFromSource($query, $source)
    {
        return $query->where('source', $source);
    }

    public function getNutritionInfoAttribute()
    {
        return [
            'calories' => $this->calories,
            'carbs' => $this->carbs,
            'protein' => $this->protein,
            'fat' => $this->fat,
        ];
    }

    public function getFormattedServingAttribute()
    {
        $serving = $this->serving_size;
        if ($this->serving_options && count($this->serving_options) > 0) {
            $serving .= ' (' . implode(', ', $this->serving_options) . ')';
        }
        return $serving;
    }
}