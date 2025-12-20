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
        Schema::create('payments', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('vendor_id');
    $table->unsignedBigInteger('purchase_order_id');
    $table->decimal('pending_amount', 10, 2);
    $table->decimal('payment_amount', 10, 2);
    $table->decimal('now_balance', 10, 2);
    $table->string('payment_type');
    $table->date('payment_date');
    $table->text('remarks')->nullable();
    $table->timestamps();

    $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
    $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
