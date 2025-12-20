<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class GeneratedBarcode extends Model
{
    use HasFactory;

    protected $table = 'generated_barcodes';

    protected $fillable = [
        'item_id',
        'mrp',
        'net_price',
        'barcode_count',
    ];

    // Optional: if you want to disable timestamps
    // public $timestamps = false;

    // Relationships (optional)
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
    
}
