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
        Schema::create('programing_employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programing_id')->constrained('programing');
            $table->foreignId('employee_id')->constrained('employees');
            $table->string("status", 144);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('programing_employees');
    }
};
