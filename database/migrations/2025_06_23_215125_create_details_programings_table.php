<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('details_programings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('programming_id')->constrained('programming')->onDelete('cascade');
            $table->foreignId('employee_id')->constrained('employees')->onDelete('restrict');
            $table->date('date_start');
            $table->string('status', 50);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('details_programings');
    }
};
