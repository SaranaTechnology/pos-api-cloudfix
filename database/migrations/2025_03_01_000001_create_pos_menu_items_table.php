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
        Schema::create('pos_menu_items', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('product_id')->nullable()->index();
            $table->string('sku')->nullable()->unique();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('price');
            $table->boolean('is_active')->default(true);
            $table->integer('loyalty_points')->default(0);
            $table->json('metadata')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_menu_items');
    }
};
