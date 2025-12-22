<?php

// app/Models/Category.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    protected $fillable = ['name', 'remarks', 'status', 'created_by'];

 public function subcategories()
    {
        return $this->hasMany(SubCategory::class); // This should work correctly now
    }

    public function items()
    {
        return $this->hasMany(Item::class);
    }

    public function creator()
    {
        return $this->belongsTo(Employee::class, 'created_by');
    }

}

