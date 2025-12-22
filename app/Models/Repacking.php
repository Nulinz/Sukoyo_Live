<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Repacking extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id', 'item_name', 'total_bulk_qty', 'bulk_unit', // Add 'item_id'
        'repack_uom', 'repack_qty', 'cost_per_pack','repacking_charge',
        'selling_price', 'variant_name', 'store_id'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    
    // Add relationship to Item model
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}