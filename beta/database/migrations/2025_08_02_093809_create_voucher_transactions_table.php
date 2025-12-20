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
Schema::create('voucher_transactions', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('voucher_id');
    $table->unsignedBigInteger('sales_invoice_id');
    $table->decimal('used_amount', 10, 2);
    $table->timestamp('transaction_date');
    $table->text('notes')->nullable();
    $table->timestamps();

    $table->foreign('voucher_id')->references('id')->on('vouchers')->onDelete('cascade');
    $table->foreign('sales_invoice_id')->references('id')->on('sales_invoices')->onDelete('cascade');

    $table->index(['voucher_id', 'transaction_date']);
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('voucher_transactions');
    }
};
