<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    use HasFactory;

    protected $table = 'gift_cards';


    protected $fillable = [
        'card_code',
        'no_of_cards',
        'card_type',
        'card_value',
        'issue_date',
        'expiry_date',
        'reloadable',
        'store_id',     // ✅ add
        'created_by', 
    ];

    protected $casts = [
        'issue_date' => 'date',
        'expiry_date' => 'date',
        'reloadable' => 'boolean',
    ];

    // Relationships
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }

    public function store()
    {
        return $this->belongsTo(\App\Models\Store::class);
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class, 'gift_card_code', 'card_code');
    }

    // Accessor to get total used amount
    public function getTotalUsedAttribute()
    {
        return $this->salesInvoices()->sum('gift_card_amount');
    }

    // Accessor to get remaining balance
    public function getBalanceAttribute()
    {
        return max(0, $this->value - $this->total_used);
    }

    // Accessor to check if card is active
    public function getIsActiveAttribute()
    {
        return $this->balance > 0 && (!$this->expiry_date || $this->expiry_date->isFuture());
    }
}