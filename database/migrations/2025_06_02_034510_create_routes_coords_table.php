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
        Schema::create('routes_coords', function (Blueprint $table) {
            $table->id();
            $table->double('index', 10, 6);
            $table->double('latitud', 10, 6);
            $table->double('longitud', 10, 6);
            $table->foreignId('routes_id')->constrained('routes')->onDelete('restrict');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes_coords');
    }
};
