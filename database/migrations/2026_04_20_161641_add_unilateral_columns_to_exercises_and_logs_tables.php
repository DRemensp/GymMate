<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->boolean('is_unilateral')->default(false)->after('description');
        });

        Schema::table('exercise_logs', function (Blueprint $table) {
            $table->unsignedInteger('reps')->nullable()->change();
            $table->unsignedInteger('reps_left')->nullable()->after('reps');
            $table->unsignedInteger('reps_right')->nullable()->after('reps_left');
        });
    }

    public function down(): void
    {
        Schema::table('exercises', function (Blueprint $table) {
            $table->dropColumn('is_unilateral');
        });

        Schema::table('exercise_logs', function (Blueprint $table) {
            $table->dropColumn(['reps_left', 'reps_right']);
            $table->unsignedInteger('reps')->nullable(false)->change();
        });
    }
};
