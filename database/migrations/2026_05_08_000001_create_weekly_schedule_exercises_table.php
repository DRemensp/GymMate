<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('weekly_schedule_exercises', function (Blueprint $table) {
            $table->foreignId('weekly_schedule_id')->constrained('weekly_schedules')->cascadeOnDelete();
            $table->foreignId('exercise_id')->constrained('exercises')->cascadeOnDelete();
            $table->primary(['weekly_schedule_id', 'exercise_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('weekly_schedule_exercises');
    }
};
