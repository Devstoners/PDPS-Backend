<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('suppliers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->tinyInteger('title'); // 1 = Mr, 2 = Mrs, 3 = Miss, 4 = Rev
            $table->string('name_en');
            $table->string('name_si');
            $table->string('name_ta');
            $table->string('image')->nullable();
            $table->string('tel');
            $table->string('company_name');
            $table->string('company_reg_no')->nullable();
            $table->text('address');
            $table->string('supply_category'); // e.g., 'construction', 'office_supplies', 'maintenance'
            $table->string('contact_person')->nullable();
            $table->string('contact_tel')->nullable();
            $table->string('contact_email')->nullable();
            $table->text('description')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('suppliers');
    }
};
