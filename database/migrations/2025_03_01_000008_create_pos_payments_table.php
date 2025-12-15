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
        Schema::create('pos_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_sale_id')->index();
            $table->enum('method', ['cash', 'debit', 'credit', 'qris', 'transfer', 'ewallet', 'other'])->default('cash');
            $table->bigInteger('amount')->default(0);
            $table->bigInteger('change')->default(0);
            $table->string('reference')->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->foreign('pos_sale_id')->references('id')->on('pos_sales')->cascadeOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_payments');
    }
};
