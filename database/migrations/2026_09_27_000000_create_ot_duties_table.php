<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ot_duties', function (Blueprint $table) {
            $table->id();
            $table->string('sister_name', 100);
            $table->string('technician_name', 100);
            $table->unsignedTinyInteger('ot_no');
            $table->string('shift', 30);
            $table->string('department', 100);
            $table->unsignedTinyInteger('unit_no');
            $table->string('surgery')->nullable();
            $table->text('remarks')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->index(['ot_no', 'shift']);
            $table->index(['department', 'unit_no']);
            $table->index('sister_name');
            $table->index('technician_name');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ot_duties');
    }
};
