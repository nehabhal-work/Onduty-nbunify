<?php

use App\Support\OtDutyOptions;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('staff_members', function (Blueprint $table) {
            $table->id();
            $table->string('type', 20);
            $table->string('name', 100);
            $table->string('mobile', 30)->nullable();
            $table->string('email')->nullable();
            $table->string('address', 500)->nullable();
            $table->timestamps();

            $table->unique(['type', 'name']);
            $table->index('type');
        });

        $now = now();
        $members = [];

        foreach (['sister', 'technician'] as $type) {
            foreach (OtDutyOptions::sisters() as $name) {
                $members[] = [
                    'type' => $type,
                    'name' => $name,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        DB::table('staff_members')->insert($members);
    }

    public function down(): void
    {
        Schema::dropIfExists('staff_members');
    }
};