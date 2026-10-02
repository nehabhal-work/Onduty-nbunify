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
            $table->string('technician_name', 100)->nullable()->change();
        });
    }

    public function down(): void
    {
        DB::table('ot_duties')->whereNull('technician_name')->update(['technician_name' => 'Not assigned']);

        Schema::table('ot_duties', function (Blueprint $table) {
            $table->string('technician_name', 100)->nullable(false)->change();
        });
    }
};