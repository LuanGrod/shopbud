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
        Schema::create('shopping_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('session_id')->constrained('shopping_sessions')->cascadeOnDelete();
            $table->string('sector_name', 256);
            $table->string('product_name', 256);
            $table->decimal('price', 10, 2);
            $table->integer('quantity');
            $table->boolean('extra');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('shopping_items');
    }
};
