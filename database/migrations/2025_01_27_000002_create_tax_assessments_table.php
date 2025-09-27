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
        Schema::create('tax_assessments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('tax_property_id');
            $table->integer('year')->notNull();
            $table->decimal('amount', 12, 2)->notNull();
            $table->date('due_date')->notNull();
            $table->enum('status', ['unpaid', 'paid', 'overdue'])->default('unpaid');
            $table->unsignedBigInteger('officer_id');
            $table->timestamps();

            // Foreign key constraints removed due to database permission limitations
            // $table->foreign('tax_property_id')->references('id')->on('tax_properties')->onDelete('cascade');
            // $table->foreign('officer_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_assessments');
    }
};
