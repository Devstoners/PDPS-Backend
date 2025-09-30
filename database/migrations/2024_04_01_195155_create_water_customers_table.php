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
        Schema::create('water_customers', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('account_no',200)->unique();
            $table->integer('title');
            $table->string('name',250);
            $table->string('nic',12);
            $table->string('tel',10);
            $table->string('address',250);
            $table->string('email');
            $table->date('con_date');
            $table->integer('water_schemes_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_customers');
    }
};
