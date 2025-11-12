<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseItems extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'purchase_id', 'product_id', 'quantity', 'purchase_price', 'total_price'
    ];
    
    public function purchase()
    {
        return $this->belongsTo(Purchases::class, 'purchase_id');
    }
    
    public function product()
    {
        return $this->belongsTo(Products::class, 'product_id');
    }
}