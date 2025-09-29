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
        Schema::create('hall_customers', function (Blueprint $table) {
            $table->id();
            $table->tinyInteger('title'); // 1=Mr, 2=Mrs, 3=Miss, 4=Rev
            $table->string('name');
            $table->string('nic')->unique();
            $table->string('tel');
            $table->text('address');
            $table->string('email')->unique();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hall_customers');
    }
};
