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
         Schema::create('pos_systems', function (Blueprint $table) {
            $table->id();
            $table->string('system_no')->unique();
            $table->string('system_type'); // store as "Retail", "Wholesale", or JSON if multiple
            $table->string('remarks')->nullable();
            $table->boolean('status')->default(true); // true=Active, false=Inactive
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pos_systems');
    }
};
