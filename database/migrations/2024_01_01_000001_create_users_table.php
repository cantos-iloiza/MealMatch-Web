<?php

// database/migrations/2024_01_01_000001_create_users_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('firebase_uid')->unique();
            $table->string('email')->unique();
            $table->string('name');
            $table->string('avatar')->nullable();
            $table->json('goals')->nullable();
            $table->string('activity_level')->default('sedentary');
            $table->string('gender');
            $table->integer('age');
            $table->decimal('height', 8, 2);
            $table->decimal('weight', 8, 2);
            $table->decimal('goal_weight', 8, 2);
            $table->integer('calorie_goal')->default(2000);
            $table->string('weight_pace')->default('steady');
            $table->boolean('scheduled_for_deletion')->default(false);
            $table->timestamp('deletion_date')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users');
    }
};