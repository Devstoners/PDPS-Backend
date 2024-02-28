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
        Schema::create('member_divisions', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('division_en',150);
            $table->string('division_si',150);
            $table->string('division_ta',150);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('member_divisions');
    }
};
