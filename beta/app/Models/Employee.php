<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    protected $fillable = [
        'empcode', 'empname', 'gender', 'marital', 'dob', 'contact', 'altcontact',
        'email', 'designation', 'store_id', 'password', 'joindate', 'pfimg', 'ad_1', 'ad_2',
        'dis', 'state', 'pin','created_by'
    ];

    protected $hidden = ['password'];

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id');
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }
     public function attendances()
    {
        return $this->hasMany(Attendance::class, 'employee_id');
    }
}
