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
        Schema::create('water_bills', function (Blueprint $table) {
            $table->id();
            $table->timestamps();
            $table->integer('water_customer_id');
            $table->integer('officer_id');
            $table->integer('year');
            $table->integer('month');
            $table->date('meter_read_date');
            $table->double('meter_read_val');
            $table->decimal('outstand_amount',2);
            $table->decimal('bill_amount',2);
            $table->date('pay_date');
            $table->tinyInteger('pay_method');
            $table->decimal('pay_amount',2);
            $table->string('transaction_id',20);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_bills');
    }
};
