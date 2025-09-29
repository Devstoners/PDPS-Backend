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
        Schema::create('water_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('water_bill_id');
            $table->decimal('amount_paid', 10, 2);
            $table->date('pay_date');
            $table->enum('pay_method', ['cash', 'online', 'bank_transfer']);
            $table->string('transaction_id')->nullable();
            $table->string('receipt_no')->nullable();
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->timestamps();

            $table->foreign('water_bill_id')->references('id')->on('water_bills')->onDelete('cascade');
            $table->foreign('officer_id')->references('id')->on('officers')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('water_payments');
    }
};
