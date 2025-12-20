<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    use HasFactory;

    protected $table = 'loyaltypoints'; // explicitly set table name

    protected $fillable = [
        'earn_amt',
        'earn_points',
        'min_invoice_for_earning',
        'redeem_amt',
        'redeem_points',
        'max_percent_invoice',
        'min_invoice_for_redeem',
        'store_id',      // ✅ add
        'created_by', 
        ];
}
