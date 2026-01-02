<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cashier_shifts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('staff_id');
            $table->string('staff_nip');
            $table->string('staff_name');
            $table->timestamp('opened_at');
            $table->timestamp('closed_at')->nullable();
            $table->decimal('opening_cash', 15, 2)->default(0);
            $table->decimal('closing_cash', 15, 2)->nullable();
            $table->decimal('expected_cash', 15, 2)->nullable();
            $table->decimal('cash_difference', 15, 2)->nullable();
            $table->integer('total_transactions')->default(0);
            $table->decimal('total_sales', 15, 2)->default(0);
            $table->decimal('total_cash_sales', 15, 2)->default(0);
            $table->decimal('total_non_cash_sales', 15, 2)->default(0);
            $table->text('notes')->nullable();
            $table->enum('status', ['open', 'closed'])->default('open');
            $table->timestamps();

            $table->index('staff_id');
            $table->index('status');
            $table->index('opened_at');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cashier_shifts');
    }
};
