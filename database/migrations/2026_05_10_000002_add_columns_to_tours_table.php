<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->boolean('cardio')->default(false)->after('profile');
            $table->boolean('following')->default(false)->after('cardio');
            $table->boolean('settings')->default(false)->after('following');
            $table->boolean('data_export')->default(false)->after('settings');
        });
    }

    public function down(): void
    {
        Schema::table('tours', function (Blueprint $table) {
            $table->dropColumn(['cardio', 'following', 'settings', 'data_export']);
        });
    }
};
