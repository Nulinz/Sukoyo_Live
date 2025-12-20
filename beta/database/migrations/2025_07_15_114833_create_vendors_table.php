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
        Schema::create('vendors', function (Blueprint $table) {
        $table->id();
        $table->string('vendorname');
        $table->string('contact', 20);
        $table->string('email');
        $table->decimal('openbalance', 10, 2);
        $table->string('tax')->nullable(); // With Tax / Without Tax
        $table->boolean('topay')->default(false);
        $table->boolean('tocollect')->default(false);
        $table->string('gst');
        $table->string('panno');
        $table->integer('creditperiod')->default(0);
        $table->decimal('creditlimit', 10, 2);
        $table->text('billaddress');
        $table->text('shipaddress');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
