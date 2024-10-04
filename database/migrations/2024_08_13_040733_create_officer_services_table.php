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
        Schema::create('officer_services', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('sname_en',500);
            $table->string('sname_si',500);
            $table->string('sname_ta',500);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('officer_services');
    }
};
