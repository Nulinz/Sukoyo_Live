<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BankTransfer extends Model
{
    use HasFactory;

    protected $fillable = [
        'transfer_from',
        'transfer_to',
        'date',
        'amount',
    ];

    public function fromBank()
    {
        return $this->belongsTo(BankAccount::class, 'transfer_from');
    }

    public function toBank()
    {
        return $this->belongsTo(BankAccount::class, 'transfer_to');
    }
}
