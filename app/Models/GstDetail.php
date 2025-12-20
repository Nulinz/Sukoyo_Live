<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GstDetail extends Model
{
    use HasFactory;

    protected $table = 'gst_details';

    protected $fillable = [
        'gst_number',
        'name',
        'business_legal',
        'contact_no',
        'email_id',
        'pan_no',
        'register_date',
        'gstaddress',
        'nature_business',
        'annual_turnover'
    ];

    protected $casts = [
        'register_date' => 'date'
    ];

    // Relationship with sales invoices
    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }
}