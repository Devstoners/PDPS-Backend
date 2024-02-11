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
        Schema::create('gallery_locales', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->string('album_desc_si',500);
            $table->string('album_desc_en',500);
            $table->string('album_desc_ta',500);

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('gallery_locales');
    }
};
