<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchases extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'purchase_date', 'purchase_number', 'supplier_id', 'cash_id', 
        'description', 'type_payment', 'status', 'total_cost', 'image'
    ];
    
    public function supplier()
    {
        return $this->belongsTo(Suppliers::class, 'supplier_id');
    }
    
    public function cash()
    {
        return $this->belongsTo(Cash::class, 'cash_id');
    }
    
    public function items()
    {
        return $this->hasMany(PurchaseItems::class, 'purchase_id');
    }
}