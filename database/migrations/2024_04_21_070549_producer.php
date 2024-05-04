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
        Schema::create('producers', function (Blueprint $table) {
            $table->id();
            $table->string('name', 255);
            $table->text('full_name')->nullable();
            $table->text('phone')->nullable();
            $table->text('email')->nullable();
            $table->integer('delivery_time');
            $table->integer('time_in_stock')->nullable();
            $table->string('currency', 20)->nullable(); // Poprawione: usunięto unsigned
            $table->bigInteger('logistic_minimum')->nullable();
            $table->timestamp('logistic_minimum_alert')->nullable();
            $table->bigInteger('order_time')->nullable();
            $table->timestamps();
            $table->softDeletes();
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
