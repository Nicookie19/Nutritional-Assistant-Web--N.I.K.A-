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
        Schema::create('feedback_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_experience_id')->nullable();
            $table->unsignedBigInteger('dietitian_id')->nullable();
            $table->string('title');
            $table->string('topic');
            $table->string('tag')->default('general');
            $table->string('tag_tone')->default('slate');
            $table->string('priority')->default('medium');
            $table->string('status')->default('pending');
            $table->text('message');
            $table->json('recommendations')->nullable();
            $table->boolean('is_read')->default(false);
            $table->date('submitted_on');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('feedback_requests');
    }
};
