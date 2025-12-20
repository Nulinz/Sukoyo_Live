<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'vendorname',
        'contact',
        'email',
        'openbalance',
        'tax',
        'topay',
        'tocollect',
        'gst',
        'panno',
        'creditperiod',
        'creditlimit',
        'billaddress',
        'shipaddress',
        'added_by',        // Who added this vendor (empcode)
        'added_by_name',   // Name of who added this vendor (optional for display)
        'status',          // Active/Inactive status
    ];

    protected $casts = [
        'topay' => 'boolean',
        'tocollect' => 'boolean',
    ];

    // Relationship to get the employee who added this vendor
    public function addedBy()
    {
        return $this->belongsTo(Employee::class, 'added_by', 'empcode');
    }
            public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class);
    }

    public function payments()
    {
        return $this->hasMany(Payment::class);
    }
}