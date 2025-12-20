<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClassModel extends Model
{
    protected $table = 'classes';

    protected $fillable = [
        'class_name', 'class_type', 'max_participants', 'pricing_type',
        'date', 'time', 'duration', 'recurring_one_time', 'tutor_id', 'store_id',
        'created_by'
    ];

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'class_name', 'class_name');
    }

    // Add relationship to Tutor
    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id');
    }
}