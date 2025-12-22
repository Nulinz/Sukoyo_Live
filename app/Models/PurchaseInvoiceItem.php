<?php

// app/Models/PurchaseInvoiceItem.php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseInvoiceItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_invoice_id',
        'purchase_order_id',
        'item',
        'unit',
        'qty',
        'price',
        'discount',
        'tax',
        'amount',
    ];

 public function item()
    {
        return $this->belongsTo(Item::class, 'item', 'id');
    }
    
    // Alternative method name to avoid confusion with column name
    public function itemDetails()
    {
        return $this->belongsTo(Item::class, 'item', 'id');
    }
        public function purchaseInvoice()
    {
        return $this->belongsTo(PurchaseInvoice::class);
    }
}
