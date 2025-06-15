<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('programing', function (Blueprint $table) {
            $table->id();
            $table->dateTime('date_joined');
            $table->foreignId('statu_id')->constrained('programing_status');
            $table->foreignId('schedule_id')->constrained('schedules');
            $table->foreignId('route_id')->constrained('routes');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programing');
    }
};
