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
         Schema::create('uoms', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('item_id'); // if each UOM is linked to an item
        $table->string('uom_type');
        $table->integer('qty');
        $table->decimal('rate_per_box', 10, 2);
        $table->integer('closing_stock')->default(0);
        $table->timestamps();

        $table->foreign('item_id')->references('id')->on('items')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('uoms');
    }
};
