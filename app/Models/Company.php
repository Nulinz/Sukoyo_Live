<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'companies';

    // Specify the fillable fields for mass assignment
    protected $fillable = [
        // Company Details
        'business_type',
        'company_name',
        'owner_name',
        'company_logo',

        // Contact Info
        'contact_number',
        'alternate_contact_number',
        'email',
        'website_url',

        // Address
        'address',
        'city',
        'state',
        'pincode',

        // Tax Info
        'gst_number',
        'pan_card_number',
        'cin_llp_number',
        'trade_license_number',

        // Bank Info
        'bank_name',
        'account_holder_name',
        'account_number',
        'ifsc_code',
        'branch_name',
    ];
}
