<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'name',
        'contact',
        'address',
        'city',
        'state',
        'pincode',
        'loyalty_points',
        'status',
        'added_by',        // Who added this customer (empcode)
        'added_by_name',   // Name of who added this customer (optional for display)
    ];

    protected $casts = [
        'loyalty_points' => 'integer',
    ];

    // Relationship to get the employee who added this customer
    public function addedBy()
    {
        return $this->belongsTo(Employee::class, 'added_by', 'empcode');
    }

    // Relationship to sales invoices
    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }

    // Scope for active customers
    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    // Scope for customers added by specific user
    public function scopeAddedBy($query, $empcode)
    {
        return $query->where('added_by', $empcode);
    }
}