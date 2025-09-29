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
        Schema::create('officers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('user_id');
            $table->integer('title');
            $table->string('name_en',250);
            $table->string('name_si',250);
            $table->string('name_ta',250);
            $table->string('image');
            $table->boolean('gender')->nullable()->default(null);
            $table->string('nic',12)->nullable()->default(null);
            $table->string('tel',10);
            $table->string('address',250)->nullable()->default(null);
            $table->boolean('is_married')->nullable()->default(null);
            $table->integer('officer_services_id');
            $table->integer('officer_grades_id');
            $table->integer('officer_positions_id');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('officers');
    }
};
