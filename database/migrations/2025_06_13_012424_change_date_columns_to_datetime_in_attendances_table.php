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
        Schema::table('attendances', function (Blueprint $table) {
            Schema::table('attendances', function (Blueprint $table) {
                $table->dateTime('date_joined')->change();
                $table->dateTime('date_end')->nullable()->change();
            });
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->date('date_joined')->change();
            $table->date('date_end')->nullable()->change();
        });
    }
};
