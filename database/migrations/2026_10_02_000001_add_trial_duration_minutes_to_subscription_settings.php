<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_settings', function (Blueprint $table) {
            $table->unsignedTinyInteger('trial_duration_minutes')->nullable()->after('trial_duration_days');
        });
    }

    public function down(): void
    {
        Schema::table('subscription_settings', function (Blueprint $table) {
            $table->dropColumn('trial_duration_minutes');
        });
    }
};