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
       Schema::create('items', function (Blueprint $table) {
    $table->id();
    $table->string('item_type');
    $table->string('item_code')->unique();
    $table->string('item_name');
    $table->foreignId('brand_id')->constrained('brands');
    $table->foreignId('category_id')->constrained('categories');
    $table->foreignId('subcategory_id')->constrained('sub_categories');
    $table->string('department');
    $table->decimal('sales_price', 10, 2);
    $table->decimal('mrp', 10, 2);
    $table->decimal('wholesale_price', 10, 2);
    $table->string('measure_unit');
    $table->integer('opening_stock');
    $table->string('opening_unit');
    $table->string('gst_rate');
    $table->text('item_description');
    $table->string('stock_status'); // Active / Inactive
    $table->integer('min_stock');
    $table->integer('max_stock');
    $table->string('abc_category');
    $table->decimal('purchase_price', 10, 2);
    $table->string('purchase_tax'); // With Tax / Without Tax
    $table->string('purchase_gst');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('items');
    }
};
