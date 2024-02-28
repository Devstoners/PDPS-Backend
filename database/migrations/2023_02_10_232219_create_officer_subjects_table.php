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
        Schema::create('officer_subjects', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('subject_en',250);
            $table->string('subject_si',250);
            $table->string('subject_ta',250);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('officer_subjects');
    }
};
