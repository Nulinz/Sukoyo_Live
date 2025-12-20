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
           Schema::create('employees', function (Blueprint $table) {
    $table->id();
    $table->string('empcode');
    $table->string('empname');
    $table->string('gender');
    $table->string('marital');
    $table->date('dob');
    $table->string('contact');
    $table->string('altcontact')->nullable();
    $table->string('email')->unique();
    $table->string('designation')->nullable();
    $table->string('password');
    $table->date('joindate');
    $table->string('pfimg')->nullable();
    $table->string('ad_1');
    $table->string('ad_2')->nullable();
    $table->string('dis');
    $table->string('state');
    $table->string('pin');
    $table->string('status')->default('Active');
    $table->timestamps();
});

    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};
