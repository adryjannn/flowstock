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
        Schema::table('producers', function (Blueprint $table) {
            $table->dropColumn(['full_name','time_in_stock', 'currency', 'logistic_minimum', 'logistic_minimum_alert', 'order_time']);
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
