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
        Schema::create('tax_penalty_notices', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('assessment_id');
            $table->date('issue_date')->notNull();
            $table->decimal('penalty_amount', 10, 2)->notNull();
            $table->enum('status', ['issued', 'resolved'])->default('issued');
            $table->timestamps();

            // Foreign key constraints removed due to database permission limitations
            // $table->foreign('assessment_id')->references('id')->on('tax_assessments')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_penalty_notices');
    }
};
