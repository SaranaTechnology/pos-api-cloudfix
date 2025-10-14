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
        Schema::create('pos_loyalty_transactions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_id')->constrained('pos_customers')->cascadeOnDelete();
            $table->unsignedBigInteger('sale_id')->nullable()->index();
            $table->string('type');
            $table->integer('points');
            $table->integer('balance_after');
            $table->string('description')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_loyalty_transactions');
    }
};
