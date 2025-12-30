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
       Schema::create('stores', function (Blueprint $table) {
        $table->id();
        $table->string('store_id')->unique();
        $table->string('store_name');
        $table->string('email');
        $table->string('contact_number');
        $table->string('address');
        $table->string('city');
        $table->string('state');
        $table->string('pincode');
        $table->string('geo_location');
         $table->string('status')->default('Active');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stores');
    }
};
