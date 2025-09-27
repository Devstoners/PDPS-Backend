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
        Schema::table('water_customers', function (Blueprint $table) {
            // Add new columns
            $table->string('account_no')->unique()->after('id');
            $table->date('dateJoin')->after('email');
            
            // Rename existing column
            $table->renameColumn('con_date', 'dateJoin');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('water_customers', function (Blueprint $table) {
            // Drop new columns
            $table->dropColumn(['account_no', 'dateJoin']);
            
            // Rename back
            $table->renameColumn('dateJoin', 'con_date');
        });
    }
};
