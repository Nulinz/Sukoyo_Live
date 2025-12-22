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
       Schema::create('customers', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('contact')->unique();
        $table->string('address')->nullable();
        $table->string('city')->nullable();
        $table->string('state')->nullable();
        $table->string('pincode')->nullable();
        $table->integer('loyalty_points')->default(0);
        $table->enum('status', ['Active', 'Inactive'])->default('Active');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
