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
        Schema::create('tutors', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('expertise');
        $table->string('email')->unique();
        $table->string('contact');
        $table->string('internal_external');
        $table->unsignedBigInteger('class_id');
        $table->text('address');
        $table->string('city');
        $table->string('state');
        $table->string('pincode');
        $table->timestamps();

        $table->foreign('class_id')->references('id')->on('classes')->onDelete('cascade');
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('tutors');
    }
};
