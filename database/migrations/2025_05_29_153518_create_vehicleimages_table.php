<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('vehicleimages', function (Blueprint $table) {
            $table->id();
            $table->string('image', 100)->nullable();
            $table->string('profile', 100);
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vehicleimages');
    }
};
