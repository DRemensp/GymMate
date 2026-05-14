<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tours', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('locations')->default(false);
            $table->boolean('plans')->default(false);
            $table->boolean('exercises')->default(false);
            $table->boolean('logging')->default(false);
            $table->boolean('weekly')->default(false);
            $table->boolean('analyse')->default(false);
            $table->boolean('profile')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tours');
    }
};
