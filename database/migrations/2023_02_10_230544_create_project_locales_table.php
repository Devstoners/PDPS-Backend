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
        Schema::create('project_locales', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('name_si',200);
            $table->string('name_en',200);
            $table->string('name_ta',200);
            $table->string('description_si',600);
            $table->string('description_en',600);
            $table->string('description_ta',600);
            $table->string('executor_si',100);
            $table->string('executor_en',100);
            $table->string('executor_ta',100);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('project_locales');
    }
};
