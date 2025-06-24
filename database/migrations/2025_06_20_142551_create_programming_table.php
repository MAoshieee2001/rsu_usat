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
        Schema::create('programming', function (Blueprint $table) {
            $table->id();
            $table->date('date_joined');
            $table->dateTime('date_start');
            $table->dateTime('date_end');

            $table->foreignId('schedule_id')->constrained('schedules');
            $table->foreignId('zone_id')->constrained('zones');
            $table->foreignId('vehicle_id')->constrained('vehicles');

            $table->json('dias_semana');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programming');
    }
};
