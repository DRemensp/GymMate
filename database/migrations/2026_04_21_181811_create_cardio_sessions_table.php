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
        Schema::create('cardio_sessions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('activity');
            $table->unsignedSmallInteger('duration_minutes');
            $table->decimal('distance_km', 5, 2)->nullable();
            $table->string('intensity');
            $table->unsignedTinyInteger('hiit_rounds')->nullable();
            $table->unsignedSmallInteger('hiit_work_seconds')->nullable();
            $table->unsignedSmallInteger('hiit_rest_seconds')->nullable();
            $table->text('notes')->nullable();
            $table->timestamp('logged_at')->useCurrent();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cardio_sessions');
    }
};
