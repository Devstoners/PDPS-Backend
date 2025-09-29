<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('officer_positions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('position_en',200);
            $table->string('position_si',200);
            $table->string('position_ta',200);
            $table->bigInteger('officer_services_id')->unsigned();
            $table->bigInteger('officer_levels_id')->unsigned();
            $table->foreign('officer_services_id')->references('id')->on('officer_services')->onDelete('cascade');
            $table->foreign('officer_levels_id')->references('id')->on('officer_levels')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('officer_positions');
    }
};
