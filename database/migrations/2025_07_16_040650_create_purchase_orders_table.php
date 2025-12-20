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
         Schema::create('purchase_orders', function (Blueprint $table) {
        $table->id();
        $table->foreignId('vendor_id')->constrained();
        $table->string('bill_no');
        $table->date('bill_date');
        $table->date('due_date')->nullable();
        $table->string('transport');
        $table->string('packaging');
        $table->string('warehouse');
        $table->string('payment_type');
        $table->string('reference_no');
        $table->text('description')->nullable();
        $table->decimal('total', 10, 2);
        $table->decimal('paid_amount', 10, 2);
        $table->decimal('balance_amount', 10, 2);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
