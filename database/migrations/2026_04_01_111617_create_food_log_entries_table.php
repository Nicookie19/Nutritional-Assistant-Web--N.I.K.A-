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
        Schema::create('food_log_entries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_experience_id')->constrained()->cascadeOnDelete();
            $table->foreignId('food_item_id')->nullable()->constrained()->nullOnDelete();
            $table->string('meal_slot');
            $table->string('food_name');
            $table->string('serving_label');
            $table->date('entry_date');
            $table->unsignedInteger('calories');
            $table->decimal('protein', 6, 1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('food_log_entries');
    }
};
