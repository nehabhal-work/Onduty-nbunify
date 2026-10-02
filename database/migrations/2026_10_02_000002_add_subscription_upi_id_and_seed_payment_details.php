<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('subscription_settings', function (Blueprint $table) {
            $table->string('upi_id')->nullable()->after('bank_details');
        });

        $settings = DB::table('subscription_settings')->where('id', 1)->first();

        if ($settings) {
            DB::table('subscription_settings')->where('id', 1)->update([
                'bank_details' => $settings->bank_details ?: "HDFC Bank (Primary)\nAccount holder: NBUNIFY PRIVATE LIMITED\nAccount No: 502000117622680\nIFSC: HDFC0002504\nMICR: -\nBranch: THANE\nUPI: 7710020126@hdfc",
                'upi_id' => $settings->upi_id ?: '7710020126@hdfc',
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        Schema::table('subscription_settings', function (Blueprint $table) {
            $table->dropColumn('upi_id');
        });
    }
};