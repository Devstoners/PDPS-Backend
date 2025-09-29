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
        Schema::create('officer_grades', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('grade_en',100);
            $table->string('grade_si',100);
            $table->string('grade_ta',100);
            $table->bigInteger('officer_services_id')->unsigned();
            $table->foreign('officer_services_id')->references('id')->on('officer_services')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officer_grades');
    }
};
