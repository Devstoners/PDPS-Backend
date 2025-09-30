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
        Schema::create('water_meter_readings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('water_customer_id');
            $table->date('reading_month');
            $table->decimal('current_reading', 10, 2);
            $table->decimal('previous_reading', 10, 2);
            $table->decimal('units_consumed', 10, 2);
            $table->boolean('submitted')->default(false);
            $table->timestamps();

            $table->foreign('water_customer_id')->references('id')->on('water_customers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_meter_readings');
    }
};
