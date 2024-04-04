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
        Schema::create('water_unit_prices', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('water_schemes_id');
            $table->integer('block_no');
            $table->decimal('unit_price',2);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_unit_prices');
    }
};
