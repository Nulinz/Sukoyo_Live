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
Schema::create('bank_transfers', function (Blueprint $table) {
        $table->id();
        $table->unsignedBigInteger('transfer_from');
        $table->unsignedBigInteger('transfer_to');
        $table->date('date');
        $table->decimal('amount', 10, 2);
        $table->timestamps();

        $table->foreign('transfer_from')->references('id')->on('bank_accounts')->onDelete('cascade');
        $table->foreign('transfer_to')->references('id')->on('bank_accounts')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bank_transfers');
    }
};
