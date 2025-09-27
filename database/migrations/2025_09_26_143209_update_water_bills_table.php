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
        Schema::table('water_bills', function (Blueprint $table) {
            // Drop old columns
            $table->dropColumn(['officer_id', 'year', 'month', 'meter_read_date', 'meter_read_val', 'outstand_amount', 'bill_amount', 'pay_date', 'pay_method', 'pay_amount']);
            
            // Add new columns
            $table->unsignedBigInteger('meter_reader_id')->after('water_customer_id');
            $table->date('billing_month')->after('meter_reader_id');
            $table->date('due_date')->after('billing_month');
            $table->decimal('amount_due', 10, 2)->after('due_date');
            $table->tinyInteger('status')->default(1)->after('amount_due'); // 1=unpaid, 2=paid, 3=overdue
            
            // Add foreign key
            $table->foreign('meter_reader_id')->references('id')->on('water_meter_readers')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_bills', function (Blueprint $table) {
            // Drop foreign key
            $table->dropForeign(['meter_reader_id']);
            
            // Drop new columns
            $table->dropColumn(['meter_reader_id', 'billing_month', 'due_date', 'amount_due', 'status']);
            
            // Restore old columns
            $table->unsignedBigInteger('officer_id');
            $table->integer('year');
            $table->integer('month');
            $table->date('meter_read_date');
            $table->decimal('meter_read_val', 10, 2);
            $table->decimal('outstand_amount', 10, 2);
            $table->decimal('bill_amount', 10, 2);
            $table->date('pay_date')->nullable();
            $table->tinyInteger('pay_method')->nullable();
            $table->decimal('pay_amount', 10, 2)->nullable();
        });
    }
};
