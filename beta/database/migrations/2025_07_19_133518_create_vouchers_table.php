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
        Schema::create('vouchers', function (Blueprint $table) {
    $table->id();
    $table->string('voucher_code');
    $table->string('voucher_name');
    $table->integer('no_of_cards');
    $table->decimal('discount_value', 10, 2);
    $table->string('redeemable_brand')->nullable();
    $table->string('redeemable_category')->nullable();
    $table->string('redeemable_subcategory')->nullable();
    $table->string('redeemable_item')->nullable();
    $table->date('issue_date');
    $table->date('expiry_date');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};
