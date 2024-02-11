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
            $table->string('email')->unique();
            $table->string('full_name',250);
            $table->string('display_name',100);
            $table->string('profile_pic');
            $table->boolean('gender');
            $table->string('nic',12);
            $table->string('tel1',10);
            $table->string('tel2',10);
            $table->string('address',250);
            $table->boolean('is_married');
            $table->boolean('is_registered');
            $table->integer('member_divisions_id');
            $table->integer('member_parties_id');
            $table->integer('user_id');
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
