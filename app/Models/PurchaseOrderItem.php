<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'purchase_order_id',
        'item_id',
        'unit',
        'qty',
        'price',
        'discount',
        'tax',
        'amount',
    ];

    /**
     * Each item belongs to purchase order
     */
    public function purchaseOrder()
    {
        return $this->belongsTo(PurchaseOrder::class);
    }

    /**
     * Each item references an actual item
     */
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
}
