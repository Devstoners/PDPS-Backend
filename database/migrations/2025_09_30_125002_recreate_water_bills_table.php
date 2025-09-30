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
            $table->unsignedBigInteger('water_customer_id');
            $table->unsignedBigInteger('meter_reader_id');
            $table->date('billing_month');
            $table->date('due_date');
            $table->decimal('amount_due', 10, 2);
            $table->tinyInteger('status')->default(1); // 1=unpaid, 2=paid, 3=overdue
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