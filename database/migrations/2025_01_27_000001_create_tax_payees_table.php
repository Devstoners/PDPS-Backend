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
        Schema::create('tax_payees', function (Blueprint $table) {
            $table->id();
            $table->integer('title')->notNull(); // 1=Mr, 2=Mrs, 3=Miss, 4=Rev
            $table->string('name', 255)->notNull();
            $table->string('nic', 12)->unique()->notNull();
            $table->string('tel', 15)->notNull();
            $table->string('address', 250)->notNull();
            $table->string('email', 255)->notNull();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tax_payees');
    }
};
