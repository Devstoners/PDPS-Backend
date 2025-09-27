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
        Schema::create('hall_rates', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hall_facility_id');
            $table->decimal('rate', 10, 2);
            $table->string('rate_type')->default('per_hour'); // per_hour, per_day, per_event
            $table->timestamps();

            $table->foreign('hall_facility_id')->references('id')->on('hall_facilities')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hall_rates');
    }
};
