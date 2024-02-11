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
        Schema::create('tree_cut_requests', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('requester_name',100);
            $table->string('requester_address',250);
            $table->string('requester_tel',10);
            $table->string('owner_name',100);
            $table->string('owner_address',250);
            $table->string('road',250);
            $table->integer('grama_divisions_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('tree_cut_requests');
    }
};
