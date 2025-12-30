<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCardTransaction extends Model
{
    use HasFactory;

    protected $table = 'gift_card_transactions';

    protected $fillable = [
        'gift_card_id',
        'sales_invoice_id',
        'used_amount',
        'transaction_date',
        'notes'
    ];

    protected $casts = [
        'used_amount' => 'decimal:2',
        'transaction_date' => 'datetime',
    ];

    /**
     * Get the gift card that owns the transaction.
     */
    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class);
    }

    /**
     * Get the sales invoice that owns the transaction.
     */
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    /**
     * Scope to filter by gift card
     */
    public function scopeByGiftCard($query, $giftCardId)
    {
        return $query->where('gift_card_id', $giftCardId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }
}