<?php

// app/Models/Subcategory.php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class Subcategory extends Model
{
  protected $table = 'subcategories'; // Add this line to specify the correct table name
    
    protected $fillable = ['name', 'category_id', 'remarks', 'status', 'created_by'];

    public function category()
    {
        return $this->belongsTo(Category::class);
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
