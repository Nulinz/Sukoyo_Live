<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'vendor_id',
        'purchase_order_id',
        'pending_amount',
        'payment_amount',
        'now_balance',
        'payment_type',
        'payment_date',
        'created_by',
        'remarks'
    ];

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }
        public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }
public function addedBy()
{
    return $this->belongsTo(Employee::class, 'created_by');
}

}
