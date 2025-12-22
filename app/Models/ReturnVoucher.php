<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnVoucher extends Model
{
    use HasFactory;

    protected $table = 'return_vouchers';

    protected $fillable = [
        'voucher_code',
        'amount',
        'expiry_date',
        'sales_invoice_id',
        'is_used'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'expiry_date' => 'date',
        'is_used' => 'boolean',
    ];

    // Relationship with SalesInvoice
    public function salesInvoice()
    {
        return $this->belongsTo(SalesInvoice::class);
    }

    // Check if voucher is expired
    public function isExpired()
    {
        return $this->expiry_date < now()->toDateString();
    }

    // Check if voucher is valid (not expired and not used)
    public function isValid()
    {
        return !$this->is_used && !$this->isExpired();
    }

    // Generate unique voucher code
    public static function generateVoucherCode()
    {
        do {
            $code = 'RV-' . strtoupper(substr(md5(uniqid(rand(), true)), 0, 8));
        } while (self::where('voucher_code', $code)->exists());

        return $code;
    }
}