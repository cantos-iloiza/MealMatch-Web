<?php

// database/migrations/2024_01_01_000003_create_foods_table.php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('foods', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('brand')->nullable();
            $table->decimal('calories', 8, 2);
            $table->decimal('carbs', 8, 2);
            $table->decimal('protein', 8, 2);
            $table->decimal('fat', 8, 2);
            $table->string('serving_size');
            $table->json('serving_options')->nullable();
            $table->boolean('is_verified')->default(false);
            $table->string('source')->default('Local');
            $table->string('usda_fdc_id')->nullable();
            $table->string('barcode')->nullable();
            $table->timestamps();
            
            $table->index('name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('foods');
    }
};