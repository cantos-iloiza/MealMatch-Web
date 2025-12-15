<?php
// app/Models/Favorite.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Favorite extends Model
{
    use HasFactory;

    protected $fillable = [
        'firebase_uid',
        'user_id',
        'recipe_id',
        'title',
        'image',
        'category'
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'firebase_uid', 'firebase_uid');
    }

    public function scopeForUser($query, $firebaseUid)
    {
        return $query->where('firebase_uid', $firebaseUid);
    }

    public static function isFavorited($firebaseUid, $recipeId)
    {
        return self::where('firebase_uid', $firebaseUid)
                   ->where('recipe_id', $recipeId)
                   ->exists();
    }

    public static function toggle($firebaseUid, $recipeId)
    {
        $favorite = self::where('firebase_uid', $firebaseUid)
                       ->where('recipe_id', $recipeId)
                       ->first();

        if ($favorite) {
            $favorite->delete();
            return false;
        } else {
            self::create([
                'firebase_uid' => $firebaseUid,
                'recipe_id' => $recipeId,
            ]);
            return true;
        }
    }
}