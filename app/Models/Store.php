<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = [
        'store_id', 'store_name', 'email', 'contact_number', 'address',
        'city', 'state', 'pincode', 'geo_location', 'status'
    ];

    public function employees()
    {
        return $this->hasMany(Employee::class);
    }

    public function salesInvoices()
    {
        return $this->hasMany(SalesInvoice::class);
    }

    public function purchaseOrders()
    {
        return $this->hasMany(PurchaseOrder::class, 'warehouse');
    }
    public function posSystems()
{
    return $this->hasMany(PosSystem::class, 'store_id', 'store_id');
}
}