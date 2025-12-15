<?php

// database/migrations/2024_01_01_000005_create_favorites_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('favorites', function (Blueprint $table) {
            $table->id();
            $table->string('firebase_uid');
            $table->string('recipe_id'); // TheMealDB recipe ID
            $table->timestamps();
            
            $table->unique(['firebase_uid', 'recipe_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('favorites');
    }
};