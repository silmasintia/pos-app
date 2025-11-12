<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpnameDetail extends Model
{
    use HasFactory;

    protected $table = 'stock_opname_detail';
    
    protected $fillable = [
        'stock_opname_id', 
        'product_id', 
        'system_stock', 
        'physical_stock', 
        'difference', 
        'description_detail'
    ];

    public function stockOpname()
    {
        return $this->belongsTo(StockOpname::class, 'stock_opname_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}