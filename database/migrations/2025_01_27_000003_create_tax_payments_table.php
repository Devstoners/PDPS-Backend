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
        Schema::create('tax_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_property_id');
            $table->unsignedBigInteger('tax_assessment_id');
            $table->unsignedBigInteger('officer_id')->nullable();
            $table->decimal('discount_amount', 12, 2)->default(0);
            $table->decimal('fine_amount', 12, 2)->default(0);
            $table->date('pay_date')->notNull();
            $table->enum('pay_method', ['cash', 'online'])->notNull();
            $table->decimal('payment', 12, 2)->notNull();
            $table->string('transaction_id', 50)->nullable();
            $table->enum('status', ['pending', 'confirmed', 'failed'])->default('confirmed');
            $table->timestamps();

            // Foreign key constraints removed due to database permission limitations
            // $table->foreign('tax_property_id')->references('id')->on('tax_properties')->onDelete('cascade');
            // $table->foreign('tax_assessment_id')->references('id')->on('tax_assessments')->onDelete('cascade');
            // $table->foreign('officer_id')->references('id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_payments');
    }
};
