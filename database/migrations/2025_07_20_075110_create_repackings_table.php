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
        Schema::create('repackings', function (Blueprint $table) {
    $table->id();
    $table->string('item_name');
    $table->decimal('total_bulk_qty', 10, 2);
    $table->string('bulk_unit'); // pcs, box, nos
    $table->string('repack_uom');
    $table->decimal('repack_qty', 10, 2)->nullable();
    $table->decimal('cost_per_pack', 10, 2);
    $table->decimal('selling_price', 10, 2);
    $table->string('variant_name');
    $table->unsignedBigInteger('store_id');
    $table->timestamps();

    $table->foreign('store_id')->references('id')->on('stores')->onDelete('cascade');
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('repackings');
    }
};
