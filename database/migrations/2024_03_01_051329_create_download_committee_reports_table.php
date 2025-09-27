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
            $table->integer('report_year');
            $table->integer('report_month');
            $table->string('name_en',255);
            $table->string('name_si',255);
            $table->string('name_ta',255);
            $table->string('file_path_en',255);
            $table->string('file_path_si',255);
            $table->string('file_path_ta',255);
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
