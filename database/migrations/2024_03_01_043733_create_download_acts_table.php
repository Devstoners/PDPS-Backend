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
        Schema::create('download_acts', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('number',10);
            $table->date('issue_date');
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
        Schema::dropIfExists('download_acts');
    }
};
