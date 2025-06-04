<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateScheduleVehiclesTable extends Migration
{
    public function up()
    {
        Schema::create('schedule_vehicles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('schedule_id')->constrained('schedules')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->string('status')->default('activo');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('schedule_vehicles');
    }
}