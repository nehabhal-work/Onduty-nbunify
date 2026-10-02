<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('ot_duties', function (Blueprint $table) {
            $table->string('section')->default('2nd floor section')->after('date_time');
            $table->unsignedTinyInteger('ot_no')->nullable()->change();
            $table->string('department', 100)->nullable()->change();
            $table->unsignedTinyInteger('unit_no')->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('ot_duties')->whereNull('ot_no')->update(['ot_no' => 1]);
        DB::table('ot_duties')->whereNull('department')->update(['department' => 'OBGY']);
        DB::table('ot_duties')->whereNull('unit_no')->update(['unit_no' => 1]);

        Schema::table('ot_duties', function (Blueprint $table) {
            $table->dropColumn('section');
            $table->unsignedTinyInteger('ot_no')->nullable(false)->change();
            $table->string('department', 100)->nullable(false)->change();
            $table->unsignedTinyInteger('unit_no')->nullable(false)->change();
        });
    }
};
