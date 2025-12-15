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
        Schema::create('pos_sales', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_no')->unique();
            $table->unsignedBigInteger('customer_id')->nullable()->index();
            $table->unsignedBigInteger('cashier_id')->nullable()->index();
            $table->dateTime('sold_at');
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('discount')->default(0);
            $table->bigInteger('tax')->default(0);
            $table->bigInteger('total')->default(0);
            $table->enum('status', ['draft', 'paid', 'cancelled', 'refunded'])->default('draft');
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('customer_id')->references('id')->on('pos_customers')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sales');
    }
};
