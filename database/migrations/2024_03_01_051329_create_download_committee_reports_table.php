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
        Schema::create('download_committee_reports', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('year');
            $table->integer('month');
            $table->string('name_en');
            $table->string('name_si');
            $table->string('name_ta');
            $table->string('file_path');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('download_committee_reports');
    }
};
