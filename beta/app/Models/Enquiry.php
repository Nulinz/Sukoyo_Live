<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Enquiry extends Model
{
    protected $fillable = [
        'enquiry_no',
        'customer_name',
        'contact_number',
        'item_name',
        'store_id',
        'employee_id',
        'status'
    ];

    public function store()
    {
        return $this->belongsTo(Store::class);
    }
    public function employee()
{
    return $this->belongsTo(Employee::class, 'employee_id');
}

}
