<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransferDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_transfer_id',
        'item_id',
        'item_name',
        'transfer_qty',
        'unit'
    ];

    public function itemTransfer()
    {
        return $this->belongsTo(ItemTransfer::class);
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}