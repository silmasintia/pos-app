<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdjustmentDetails extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_id', 
        'product_id', 
        'name', 
        'product_code', 
        'quantity', 
        'reason'
    ];

    public function adjustment()
    {
        return $this->belongsTo(Adjustments::class, 'adjustment_id');
    }

    public function product()
    {
        return $this->belongsTo(Products::class);
    }
}
