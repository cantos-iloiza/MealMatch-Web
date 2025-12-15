<?php

// database/migrations/2024_01_01_000004_create_recipes_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipes', function (Blueprint $table) {
            $table->id();
            $table->string('firebase_uid'); // User who created it
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->integer('calories')->nullable();
            $table->json('nutrients')->nullable(); // {Protein: 25, Carbs: 30, Fat: 15}
            $table->json('ingredients')->nullable();
            $table->json('instructions')->nullable();
            $table->integer('prep_time')->nullable();
            $table->integer('cook_time')->nullable();
            $table->integer('servings')->nullable();
            $table->timestamps();
            
            $table->index('firebase_uid');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipes');
    }
};