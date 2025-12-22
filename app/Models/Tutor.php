<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'expertise',
        'email',
        'contact',
        'internal_external',
        'address',
        'city',
        'state',
        'pincode',
        'store_id',
        'added_by',
        'status',
    ];

    // Relationship with ClassModel
    public function classes()
    {
        return $this->hasMany(ClassModel::class, 'tutor_id');
    }
           public function class()
    {
        return $this->belongsTo(ClassModel::class, 'class_id');
    }
}