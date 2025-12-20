<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBookingsTable extends Migration
{
    public function up()
    {
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            
            // Student Details
            $table->string('student_id');
            $table->string('student_name');
            $table->string('email');
            $table->string('contact_number');
            $table->date('date_of_birth');
            $table->enum('gender', ['Male', 'Female', 'Others']);
            $table->string('guardian_name');
            $table->string('emergency_contact');
            
            // Address Details
            $table->text('address');
            $table->string('city');
            $table->string('state');
            $table->string('pincode');
            
            // Class Booking Details
            $table->string('class_type');
            $table->string('class_name');
            $table->date('booking_date');
            $table->time('booking_time');
            
            // Pricing & Payment
            $table->string('membership');
            $table->decimal('price', 10, 2);
               $table->string('status')->default('Active');
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('bookings');
    }
}