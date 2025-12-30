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
         Schema::create('gift_cards', function (Blueprint $table) {
        $table->id();
        $table->string('card_code');
        $table->integer('no_of_cards');
        $table->string('card_type');
        $table->decimal('card_value', 10, 2);
        $table->date('issue_date');
        $table->date('expiry_date');
        $table->boolean('reloadable')->default(false);
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('gift_cards');
    }
};
