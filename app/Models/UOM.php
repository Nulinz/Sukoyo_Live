<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UOM extends Model
{
    use HasFactory;

    protected $table = 'uoms'; // your table name

    protected $fillable = [
        'item_id', 'uom_type', 'qty', 'rate_per_box', 'closing_stock'
    ];

      public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
