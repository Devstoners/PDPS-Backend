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
        Schema::create('tree_cut_responds', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->date('inspection_date');
            $table->time('inspection_time');
            $table->string('img1');
            $table->string('img2');
            $table->tinyInteger('status');
            $table->integer('tree_cut_requests_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tree_cut_responds');
    }
};
