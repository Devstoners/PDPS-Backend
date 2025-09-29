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
        Schema::create('stripe_payments', function (Blueprint $table) {
            $table->id();
            $table->string('stripe_session_id')->unique();
            $table->string('stripe_payment_intent_id')->nullable();
            $table->string('payment_id')->unique(); // Our internal payment ID
            $table->decimal('amount', 10, 2);
            $table->string('currency', 3)->default('lkr');
            $table->enum('status', ['pending', 'processing', 'succeeded', 'completed', 'failed', 'canceled'])->default('pending');
            $table->string('tax_type');
            $table->string('taxpayer_name');
            $table->string('nic');
            $table->string('email');
            $table->string('phone')->nullable();
            $table->text('address')->nullable();
            $table->json('stripe_metadata')->nullable();
            $table->json('stripe_response')->nullable();
            $table->timestamp('paid_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stripe_payments');
    }
};
