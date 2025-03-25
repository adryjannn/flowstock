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
        Schema::create('shop_orders_product', function (Blueprint $table) {
            $table->id();
            $table->foreignId('id_order')->constrained('shop_orders')->onDelete('cascade');
            $table->integer('id_shop_order');
            $table->string('product_code');
            $table->string('product_name');
            $table->decimal('product_price', 8, 2);
            $table->integer('quantity');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
