<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PosSystem extends Model {

    protected $fillable = [
    'system_no', 'system_type', 'remarks', 'status', 'store_id', 'created_by'
];

public function store()
{
    return $this->belongsTo(Store::class, 'store_id');
}

 public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }




}