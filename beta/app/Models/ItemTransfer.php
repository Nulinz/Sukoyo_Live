<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id',
        'transfer_item_count',
        'remarks',
        'transfer_date',
        'transferred_by'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    public function transferDetails()
    {
        return $this->hasMany(ItemTransferDetail::class);
    }
}