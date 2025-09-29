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
        Schema::create('tax_properties', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('division_id');
            $table->integer('tax_payee_id');
            $table->string('street',255);
            $table->integer('property_type');
            $table->string('property_name',255);
            $table->boolean('property_prohibition')->default(0);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_properties');
    }
};
