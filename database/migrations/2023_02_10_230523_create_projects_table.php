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
        Schema::create('projects', function (Blueprint $table) {
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
            $table->decimal('budget',12,2);
            $table->date('start_date');
            $table->date('finish_date');
            $table->tinyInteger('status');//1 = Not started, 2 = Ongoing, 3 = Completed
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('projects');
    }
};
