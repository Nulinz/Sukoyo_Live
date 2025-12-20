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
        // database/migrations/xxxx_xx_xx_create_purchase_invoices_table.php

Schema::create('purchase_invoices', function (Blueprint $table) {
    $table->id();
    $table->unsignedBigInteger('purchase_order_id'); // pos_id
    $table->string('contact')->nullable();
    $table->text('billaddress')->nullable();
    $table->string('bill_no')->nullable();
    $table->date('due_date')->nullable();
    $table->date('bill_date')->nullable();
    $table->string('transport')->nullable();
    $table->string('packaging')->nullable();
    $table->string('warehouse')->nullable();
    $table->string('payment_type')->nullable();
    $table->string('reference_no')->nullable();
    $table->text('description')->nullable();
    $table->decimal('total', 10, 2)->nullable();
    $table->decimal('paid_amount', 10, 2)->nullable();
    $table->decimal('balance_amount', 10, 2)->nullable();
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_invoices');
    }
};
