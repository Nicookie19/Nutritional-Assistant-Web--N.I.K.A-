<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (Schema::hasTable('planned_meal_entries')) {
            Schema::table('planned_meal_entries', function (Blueprint $table) {
                $table->unique(['user_experience_id', 'scheduled_date', 'meal_slot'], 'planned_meals_user_date_slot_unique');
            });

            return;
        }

        Schema::create('planned_meal_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_experience_id')->constrained()->cascadeOnDelete();
            $table->date('scheduled_date');
            $table->string('meal_slot');
            $table->string('food_name');
            $table->unsignedInteger('grams');
            $table->unsignedInteger('calories');
            $table->timestamps();

            $table->unique(['user_experience_id', 'scheduled_date', 'meal_slot'], 'planned_meals_user_date_slot_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('planned_meal_entries');
    }
};
