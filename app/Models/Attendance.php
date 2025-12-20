<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = [
        'employee_id',
        'date',
        'in_time',
        'out_time',
        'break_out',
        'break_in',
        'status',
        'created_by'
    ];
    
    protected $dates = ['date'];
    
    public function employee()
    {
        return $this->belongsTo(Employee::class);
    }
    
    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }
}