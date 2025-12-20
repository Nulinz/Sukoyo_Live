<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'expense_category_id',
        'expense_no',
        'date',
        'vendor',
        'payment_type',
        'amount',
        'balance',
    ];

    public function category()
    {
        return $this->belongsTo(ExpenseCategory::class, 'expense_category_id');
    }


public function vendor()
{
    return $this->belongsTo(Vendor::class, 'vendor_id');
}

}
