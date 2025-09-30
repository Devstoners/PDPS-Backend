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
        Schema::create('water_bill_rates', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('water_schemes_id');
            $table->decimal('units_0_1', 10, 2);
            $table->decimal('units_1_5', 10, 2);
            $table->decimal('units_above_5', 10, 2);
            $table->decimal('service', 10, 2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_bill_rates');
    }
};