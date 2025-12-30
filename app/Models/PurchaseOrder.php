<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'bill_no',
        'bill_date',
        'due_date',
        'transport',
        'packaging',
        'warehouse',
        'payment_type',
        'reference_no',
        'description',
        'total',
        'paid_amount',
        'balance_amount',
        'contact',
        'billaddress'
    ];

    /**
     * Purchase order belongs to a vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Purchase order has many items
     */
    public function items()
    {
        return $this->hasMany(PurchaseOrderItem::class);
    }

    
}
