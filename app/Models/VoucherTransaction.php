<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VoucherTransaction extends Model
{
    use HasFactory;

    protected $table = 'voucher_transactions';

    protected $fillable = [
        'voucher_id',
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
     * Get the voucher that owns the transaction.
     */
    public function voucher()
    {
        return $this->belongsTo(Voucher::class);
    }

    /**
     * Get the sales invoice that owns the transaction.
     */
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    /**
     * Scope to filter by voucher
     */
    public function scopeByVoucher($query, $voucherId)
    {
        return $query->where('voucher_id', $voucherId);
    }

    /**
     * Scope to filter by date range
     */
    public function scopeByDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('transaction_date', [$startDate, $endDate]);
    }
}