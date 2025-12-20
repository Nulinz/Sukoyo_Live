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
        Schema::create('classes', function (Blueprint $table) {
        $table->id();
        $table->string('class_name');
        $table->string('class_type');
        $table->integer('max_participants');
        $table->string('pricing_type');
        $table->date('date');
        $table->time('time');
        $table->string('duration');
        $table->string('recurring_one_time');
        $table->unsignedBigInteger('tutor_id');
        $table->timestamps();
    });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('classes');
    }
};
