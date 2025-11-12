<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProductUnits extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id', 'unit_id', 'conversion_factor', 'purchase_price', 'cost_price',
        'price_before_discount', 'is_base', 'note'
    ];

public function product()
{
    return $this->belongsTo(Products::class, 'product_id');
}


    public function unit()
    {
        return $this->belongsTo(Units::class);
    }
}