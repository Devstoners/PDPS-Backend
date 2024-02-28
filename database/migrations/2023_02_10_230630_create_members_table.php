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
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('user_id');
            $table->string('name_en',250);
            $table->string('name_si',250);
            $table->string('name_ta',250);
            $table->string('image');
            $table->boolean('gender');
            $table->string('nic',12);
            $table->string('tel',10);
            $table->string('address',250);
            $table->boolean('is_married');
            $table->integer('member_divisions_id');
            $table->integer('member_parties_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('members');
    }
};
