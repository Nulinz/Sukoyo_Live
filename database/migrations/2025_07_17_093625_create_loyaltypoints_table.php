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
        Schema::create('loyaltypoints', function (Blueprint $table) {
        $table->id();
        $table->decimal('earn_amt', 10, 2);         // e.g., ₹100
        $table->integer('earn_points');            // e.g., 1
        $table->decimal('min_invoice_for_earning', 10, 2); // e.g., ₹500

        $table->decimal('redeem_amt', 10, 2);       // e.g., ₹10
        $table->integer('redeem_points');           // e.g., 1
        $table->decimal('max_percent_invoice', 5, 2); // e.g., 50%
        $table->decimal('min_invoice_for_redeem', 10, 2); // e.g., ₹20

        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('loyaltypoints');
    }
};
