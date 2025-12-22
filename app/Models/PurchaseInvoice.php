<?php

// app/Models/PurchaseInvoice.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'contact',
        'billaddress',
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
    ];


public function purchaseOrder()
{
    return $this->belongsTo(\App\Models\PurchaseOrder::class, 'purchase_order_id');
}
// public function purchaseInvoiceItems()
// {
//     return $this->hasMany(PurchaseInvoiceItem::class, 'id'); // or 'sales_invoice_id' if that's your column
// }
public function purchaseInvoiceItems()
   {
       return $this->hasMany(PurchaseInvoiceItem::class, 'purchase_invoice_id');
   }

}
