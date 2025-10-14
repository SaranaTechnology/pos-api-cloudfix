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
        Schema::create('pos_combo_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('combo_id')->constrained('pos_combos')->cascadeOnDelete();
            $table->foreignId('menu_item_id')->constrained('pos_menu_items')->cascadeOnDelete();
            $table->unsignedInteger('quantity')->default(1);
            $table->timestamps();
            $table->unique(['combo_id', 'menu_item_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_combo_items');
    }
};
