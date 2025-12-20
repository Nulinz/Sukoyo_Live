<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoiceItem extends Model
{
    use HasFactory;

    protected $table = 'sales_invoice_items';

    protected $fillable = [
        'salesinvoice_id',
        'item_id',
        'batch_id',  // Add this field
        'unit',
        'qty',
        'price',
        'discount',
        'tax',
        'amount'
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'price' => 'decimal:2',
        'discount' => 'decimal:2',
        'tax' => 'decimal:2',
        'amount' => 'decimal:2',
    ];

    // Relationships
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class, 'salesinvoice_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    // Relationship to batch (optional - can be null for regular items)
    public function batch()
    {
        return $this->belongsTo(Batch::class);
    }

    // Accessor to check if this is a batch item
    public function getIsBatchItemAttribute()
    {
        return !is_null($this->batch_id);
    }

    // Accessor to get display name (with batch info if applicable)
    public function getDisplayNameAttribute()
    {
        if ($this->is_batch_item && $this->batch) {
            return $this->item->item_name . ' (Batch: ' . $this->batch->batch_no . ')';
        }
        return $this->item->item_name;
    }

    // Scope to filter only batch items
    public function scopeBatchItems($query)
    {
        return $query->whereNotNull('batch_id');
    }

    // Scope to filter only regular items
    public function scopeRegularItems($query)
    {
        return $query->whereNull('batch_id');
    }
}