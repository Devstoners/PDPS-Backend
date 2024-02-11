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
        Schema::create('water_supplies', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('service');
            $table->string('scheme');
            $table->string('aname');
            $table->string('tele');
             $table->string('distance');
            $table->string('taxNo');
            $table->unsignedBigInteger('user_id');
            $table->foreign('user_id')->references('id')->on('users');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_supplies');
    }
};
