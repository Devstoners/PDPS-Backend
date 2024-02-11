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
        Schema::create('tree_cut_respond_details', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->decimal('distance',3,2);//In meters/feets
            $table->decimal('tree_height',3,2);//In feets
            $table->decimal('tree_girth',3,2);//In inches
            $table->string('recommendation',250);//In inches
            $table->integer('tree_cut_request_details_id');
            $table->integer('tree_cut_responds_id');         
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tree_cut_respond_details');
    }
};
