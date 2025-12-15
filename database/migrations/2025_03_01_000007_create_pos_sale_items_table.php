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
        Schema::create('pos_sale_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('pos_sale_id')->index();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->unsignedBigInteger('menu_item_id')->nullable()->index();
            $table->string('product_name')->nullable();
            $table->decimal('qty', 10, 2)->default(1);
            $table->bigInteger('price')->default(0);
            $table->bigInteger('discount')->default(0);
            $table->bigInteger('line_total')->default(0);
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->foreign('pos_sale_id')->references('id')->on('pos_sales')->cascadeOnDelete();
            $table->foreign('menu_item_id')->references('id')->on('pos_menu_items')->nullOnDelete();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_sale_items');
    }
};
