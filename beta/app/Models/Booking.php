<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'bookings';

    protected $fillable = [
        'employee_id','student_id', 'student_name', 'email', 'contact_number', 
        'date_of_birth', 'gender', 'guardian_name', 'emergency_contact',
        'address', 'city', 'state', 'pincode',
        'class_type', 'class_name', 'booking_date', 'booking_time',
        'membership', 'price','store_id',    // Add this
        'booked_by'
    ];

    protected $dates = [
        'date_of_birth',
        'booking_date'
    ];

    // Relationship with ClassModel if needed
    public function classInfo()
    {
        return $this->belongsTo(ClassModel::class, 'class_name', 'class_name');
    }
    
}