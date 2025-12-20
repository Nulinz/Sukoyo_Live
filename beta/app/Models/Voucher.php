<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $table = 'vouchers';

    protected $fillable = [
        'voucher_code',
        'voucher_name',
        'description',
        'discount_type', // 'fixed' or 'percentage'
        'discount_value',
        'minimum_amount',
        'maximum_discount',
        'usage_limit',
        'customer_id', // if voucher is for specific customer
        'employee_id', // who created the voucher
        'store_id', // if voucher is for specific store
        'start_date',
        'expiry_date',
        'is_active',
        'terms_conditions'
    ];

    protected $casts = [
        'discount_value' => 'decimal:2',
        'minimum_amount' => 'decimal:2',
        'maximum_discount' => 'decimal:2',
        'usage_limit' => 'integer',
        'start_date' => 'datetime',
        'expiry_date' => 'datetime',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function store()
    {
        return $this->belongsTo(\App\Models\Store::class);
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'voucher_code', 'voucher_code');
    }

    // Accessor to get total used amount
    public function getTotalUsedAttribute()
    {
        return $this->salesInvoices()->sum('voucher_amount');
    }

    // Accessor to get usage count
    public function getUsageCountAttribute()
    {
        return $this->salesInvoices()->count();
    }

    // Accessor to get remaining uses
    public function getRemainingUsesAttribute()
    {
        if ($this->usage_limit <= 0) {
            return 'Unlimited';
        }
        return max(0, $this->usage_limit - $this->usage_count);
    }

    // Accessor to check if voucher is active
    public function getIsValidAttribute()
    {
        $now = now();
        
        // Check if voucher is active
        if (!$this->is_active) {
            return false;
        }
        
        // Check start date
        if ($this->start_date && $this->start_date->isFuture()) {
            return false;
        }
        
        // Check expiry date
        if ($this->expiry_date && $this->expiry_date->isPast()) {
            return false;
        }
        
        // Check usage limit
        if ($this->usage_limit > 0 && $this->usage_count >= $this->usage_limit) {
            return false;
        }
        
        return true;
    }

    // Scope for active vouchers
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
                    ->where(function($q) {
                        $q->whereNull('start_date')
                          ->orWhere('start_date', '<=', now());
                    })
                    ->where(function($q) {
                        $q->whereNull('expiry_date')
                          ->orWhere('expiry_date', '>=', now());
                    });
    }

    // Method to calculate discount amount for a given total
    public function calculateDiscount($total)
    {
        if (!$this->is_valid) {
            return 0;
        }

        if ($this->minimum_amount && $total < $this->minimum_amount) {
            return 0;
        }

        if ($this->discount_type === 'percentage') {
            $discount = ($total * $this->discount_value) / 100;
            
            if ($this->maximum_discount && $discount > $this->maximum_discount) {
                $discount = $this->maximum_discount;
            }
        } else {
            $discount = $this->discount_value;
        }

        return min($discount, $total);
    }
}