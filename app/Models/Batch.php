<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Batch extends Model
{
    use HasFactory;

    protected $table = 'batches';

    protected $fillable = [
        'item_code',
        'item_id',
        'batch_no',
        'price',
        'qty',
        'mfg_date',
        'exp_date'
    ];

    protected $casts = [
        'mfg_date' => 'date',
        'exp_date' => 'date',
        'price' => 'decimal:2'
    ];

    // Relationship to Item
    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}