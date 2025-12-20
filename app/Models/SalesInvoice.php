<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SalesInvoice extends Model
{
    use HasFactory;

    protected $table = 'sales_invoices';

    protected $fillable = [
        'customer_id',
        'employee_id',        // 🆕 Added
        'store_id',          // 🆕 Added
         'pos_ipaddress', 
        'sub_total',
        'total_discount',
        'total_tax',
        'additional_charges',
        'grand_total',
        'received_amount',
        'mode_of_payment',
        'cash_amount',
        'online_amount',
        'loyalty_points_used',
        'loyalty_points_earned',
        'invoice_date',
        'gift_card_code',
        'gift_card_amount',
        'voucher_code',
        'voucher_amount',
        'return_voucher_code',
        'return_voucher_amount',
        'total_gift_card_discount',
        'status',
        'gst_detail_id',        // Add this
        'is_corporate_bill'   
    ];

    protected $casts = [
        'sub_total' => 'decimal:2',
        'total_discount' => 'decimal:2',
        'total_tax' => 'decimal:2',
        'additional_charges' => 'decimal:2',
        'grand_total' => 'decimal:2',
        'received_amount' => 'decimal:2',
        'loyalty_points_used' => 'integer',
        'loyalty_points_earned' => 'integer',
         'gift_card_amount' => 'decimal:2',
        'voucher_amount' => 'decimal:2',
        'total_gift_card_discount' => 'decimal:2',
        'invoice_date' => 'datetime',
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

    public function items()
    {
        return $this->hasMany(SalesInvoiceItem::class, 'salesinvoice_id');
    }

    // Boot method to set default values
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (!$model->invoice_date) {
                $model->invoice_date = now();
            }
            if (!$model->status) {
                $model->status = 'completed';
            }
        });
    }

    // Scopes for filtering
    public function scopeByEmployee($query, $employee_id)
    {
        return $query->where('employee_id', $employee_id);
    }

    public function scopeByStore($query, $store_id)
    {
        return $query->where('store_id', $store_id);
    }

    public function scopeByDateRange($query, $start_date, $end_date)
    {
        return $query->whereBetween('invoice_date', [$start_date, $end_date]);
    }

    // Accessor to get employee name
    public function getEmployeeNameAttribute()
    {
        return $this->employee ? $this->employee->empname : 'N/A';
    }

    // Accessor to get store name
    public function getStoreNameAttribute()
    {
        return $this->store ? $this->store->store_name : 'N/A';
    }

    public function scopeByIpAddress($query, $ipAddress)
    {
        return $query->where('pos_ipaddress', 'LIKE', "%{$ipAddress}%");
    }

    /**
     * Scope to filter by date range
     */
    public function scopeDateRange($query, $startDate, $endDate)
    {
        return $query->whereBetween('invoice_date', [$startDate, $endDate]);
    }

    /**
     * Get formatted IP address for display
     */
    public function getFormattedIpAttribute()
    {
        if (!$this->pos_ipaddress) {
            return 'N/A';
        }

        // If it contains hostname in parentheses, format it nicely
        if (strpos($this->pos_ipaddress, '(') !== false) {
            return $this->pos_ipaddress;
        }

        return $this->pos_ipaddress;
    }

    /**
     * Check if transaction was made from local network
     */
    public function getIsLocalTransactionAttribute()
    {
        if (!$this->pos_ipaddress) {
            return false;
        }

        $localRanges = [
            '127.0.0.1',
            '::1',
            '10.',
            '192.168.',
            '172.16.',
            '172.17.',
            '172.18.',
            '172.19.',
            '172.20.',
            '172.21.',
            '172.22.',
            '172.23.',
            '172.24.',
            '172.25.',
            '172.26.',
            '172.27.',
            '172.28.',
            '172.29.',
            '172.30.',
            '172.31.'
        ];

        foreach ($localRanges as $range) {
            if (strpos($this->pos_ipaddress, $range) === 0) {
                return true;
            }
        }

        return false;
    }

    /**
     * Get location info from IP (you can integrate with IP geolocation services)
     */
    public function getIpLocationAttribute()
    {
        if (!$this->pos_ipaddress || $this->is_local_transaction) {
            return 'Local Network';
        }

        // You can integrate with services like MaxMind, IPinfo, etc.
        // For now, return basic info
        return 'External Network';
    }


    // New relationships for Gift Cards and Vouchers
    public function giftCard()
    {
        return $this->belongsTo(GiftCard::class, 'gift_card_code', 'card_code');
    }

    public function voucher()
    {
        return $this->belongsTo(Voucher::class, 'voucher_code', 'voucher_code');
    }

    // Scope to get invoices with gift cards
    public function scopeWithGiftCards($query)
    {
        return $query->whereNotNull('gift_card_code')->orWhereNotNull('voucher_code');
    }

    // Accessor to get total discount including gift cards
    public function getTotalDiscountWithGiftCardsAttribute()
    {
        return $this->total_discount + $this->total_gift_card_discount;
    }

    // Accessor to check if gift card was used
    public function getHasGiftCardAttribute()
    {
        return !is_null($this->gift_card_code) || !is_null($this->voucher_code);
    }
     public function gstDetail()
{
    return $this->belongsTo(GstDetail::class);
}

// Add this scope to filter corporate bills
public function scopeCorporateBills($query)
{
    return $query->where('is_corporate_bill', true);
}
}