<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Products extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_code', 'barcode', 'name', 'slug', 'category_id', 'description', 'image',
        'status_active', 'status_discount', 'status_display', 'note', 'position', 'reminder',
        'link', 'expire_date', 'sold', 'base_unit_id', 'base_stock'
    ];

    public function category()
    {
        return $this->belongsTo(Categories::class, 'category_id');
    }

    public function productUnits()
    {
        return $this->hasMany(ProductUnits::class, 'product_id');
    }

    public function baseUnit()
    {
        return $this->belongsTo(Units::class, 'base_unit_id');
    }

    public function images()
    {
        return $this->hasMany(ProductImages::class, 'product_id');
    }

    public function purchaseItems()
    {
        return $this->hasMany(PurchaseItems::class);
    }
    
    public function orderItems()
    {
        return $this->hasMany(OrderItems::class, 'product_id');
    }

    
    
}