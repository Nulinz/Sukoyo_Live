<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    protected $fillable = ['name', 'remarks', 'status', 'created_by'];

    public function creator()
    {
        return $this->belongsTo(\App\Models\Employee::class, 'created_by');
    }
}
