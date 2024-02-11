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
        Schema::create('tree_cut_request_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();          
            $table->string('tree_type',100);
            $table->tinyInteger('tree_count');
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
        Schema::dropIfExists('tree_cut_request_details');
    }
};
