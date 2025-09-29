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
        Schema::create('hall_customer_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('hall_reservation_id');
            $table->decimal('pay_amount', 10, 2);
            $table->date('pay_date');
            $table->tinyInteger('pay_method'); // 1=Cash, 2=Card, 3=Bank Transfer
            $table->string('transaction_id')->nullable();
            $table->tinyInteger('payment_status')->default(0); // 0=pending, 1=completed
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('hall_reservation_id')->references('id')->on('hall_reservations')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('hall_customer_payments');
    }
};
