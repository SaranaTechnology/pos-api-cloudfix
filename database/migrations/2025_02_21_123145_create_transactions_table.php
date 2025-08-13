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
        Schema::create('transactions', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('client_id');
            $table->bigInteger('plan_id')->nullable();
            $table->string('plan_name');
            $table->string('transaction_sid')->nullable();
            $table->string('transaction_id_ipaymu')->nullable();
            $table->string('status')->default('pending');
            $table->double('buyer_payment');
            $table->double('net_payment');
            $table->double('transaction_fee');
            $table->dateTime('payment_at')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('bukti_pembayaran')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
