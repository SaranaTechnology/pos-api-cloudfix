<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('self_orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_no', 20)->unique();
            $table->string('customer_name', 100);
            $table->string('customer_phone', 20)->nullable();
            $table->string('table_no', 10)->nullable();
            $table->unsignedBigInteger('customer_id')->nullable();
            $table->bigInteger('subtotal')->default(0);
            $table->bigInteger('tax')->default(0);
            $table->bigInteger('total')->default(0);
            $table->string('status', 20)->default('pending');
            $table->text('notes')->nullable();
            $table->unsignedBigInteger('pos_sale_id')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('created_at');
            $table->index('customer_id');
            $table->index('pos_sale_id');
        });

        Schema::create('self_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('self_order_id')->constrained('self_orders')->cascadeOnDelete();
            $table->unsignedBigInteger('menu_item_id')->nullable();
            $table->unsignedBigInteger('combo_id')->nullable();
            $table->string('item_name', 100);
            $table->integer('qty')->default(1);
            $table->bigInteger('price')->default(0);
            $table->bigInteger('line_total')->default(0);
            $table->string('notes', 200)->nullable();
            $table->timestamps();

            $table->index('menu_item_id');
            $table->index('combo_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('self_order_items');
        Schema::dropIfExists('self_orders');
    }
};
