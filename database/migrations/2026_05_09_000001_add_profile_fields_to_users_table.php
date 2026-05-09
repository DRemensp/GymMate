<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->decimal('weight_kg', 5, 2)->nullable()->after('target_reps');
            $table->unsignedSmallInteger('height_cm')->nullable()->after('weight_kg');
            $table->enum('gender', ['männlich', 'weiblich', 'divers'])->nullable()->after('height_cm');
        });
    }

    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['weight_kg', 'height_cm', 'gender']);
        });
    }
};
