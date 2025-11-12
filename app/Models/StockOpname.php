<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockOpname extends Model
{
    use HasFactory;

    protected $table = 'stock_opname';
    
    protected $fillable = [
        'opname_number', 
        'opname_date', 
        'description', 
        'image'
    ];

     protected $dates = [
        'opname_date',
    ];

    public function details()
    {
        return $this->hasMany(StockOpnameDetail::class);
    }
}
