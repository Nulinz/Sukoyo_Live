<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    // Table name (optional if table name is 'items')
    protected $table = 'items';

    // Mass assignable fields
    protected $fillable = [
        'item_type',
        'item_code',
         'hsn_code',
        'item_name',
        'brand_id',
        'category_id',
        'subcategory_id',
        'discount',
        'sales_price',
         'mrp',
        'wholesale_price',
        'measure_unit',
        'opening_stock',
        'opening_unit',
        'gst_rate',
        'item_description',
        'stock_status',
        'min_stock',
        'max_stock',
        'abc_category',
        'purchase_price',
        'purchase_tax',
        'purchase_gst',
        'store_id'
    ];

    // Relationships
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

public function uoms()
{
    return $this->hasMany(UOM::class); // ✅ Correct class name
}


    public function category()
    {
        return $this->belongsTo(Category::class);
    }

   public function subcategory()
{
    return $this->belongsTo(SubCategory::class, 'subcategory_id');
}

public function purchaseOrderItems()
{
    return $this->hasMany(PurchaseOrderItem::class);
}
public function salesInvoiceItems()
{
    return $this->hasMany(SalesInvoiceItem::class, 'item_id');
}
    public function purchaseInvoiceItems()
    {
        return $this->hasMany(PurchaseInvoiceItem::class, 'item', 'id');
    }
    public function store()
    {
        return $this->belongsTo(Store::class);
    }
 public function batches()
    {
        return $this->hasMany(Batch::class);
    }
}
