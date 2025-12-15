<?php
// database/migrations/2024_01_01_000002_create_meal_logs_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{  
    public function up(): void
    {
        Schema::create('meal_logs', function (Blueprint $table) {
            $table->id();
            $table->string('firebase_uid'); // User's Firebase UID
            $table->string('category'); // Breakfast, Lunch, Dinner, Snacks
            $table->string('food_name');
            $table->string('brand')->nullable();
            $table->decimal('calories', 8, 2);
            $table->decimal('carbs', 8, 2);
            $table->decimal('fats', 8, 2);
            $table->decimal('proteins', 8, 2);
            $table->string('serving');
            $table->date('date');
            $table->boolean('is_verified')->default(false);
            $table->string('source')->default('Local');
            $table->string('recipe_id')->nullable();
            $table->timestamps();
            
            $table->index(['firebase_uid', 'date']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('meal_logs');
    }
};