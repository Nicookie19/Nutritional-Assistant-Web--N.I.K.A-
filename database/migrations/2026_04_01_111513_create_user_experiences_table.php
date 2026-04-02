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
        Schema::create('user_experiences', function (Blueprint $table) {
            $table->id();
            $table->string('session_key')->unique();
            $table->unsignedBigInteger('active_meal_plan_id')->nullable();
            $table->foreignId('active_dietitian_id')->nullable()->constrained('dietitians')->nullOnDelete();
            $table->string('full_name')->default('');
            $table->unsignedTinyInteger('age')->default(28);
            $table->string('gender')->default('Male');
            $table->string('activity_level')->default('');
            $table->string('primary_goal')->default('');
            $table->unsignedSmallInteger('height_cm')->default();
            $table->decimal('current_weight_kg', 5, 1)->default();
            $table->decimal('target_weight_kg', 5, 1)->default();
            $table->decimal('starting_weight_kg', 5, 1)->default();
            $table->json('bmi_history')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_experiences');
    }
};
