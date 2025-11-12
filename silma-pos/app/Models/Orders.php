<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Orders extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_date',
        'order_number',
        'customer_id',
        'cash_id',
        'total_cost_before',
        'percent_discount',
        'amount_discount',
        'input_payment',
        'return_payment',
        'total_cost',
        'status',
        'image',
        'description',
        'type_payment'
    ];

    public function customer() {
        return $this->belongsTo(Customers::class);
    }

    public function cash() {
        return $this->belongsTo(Cash::class);
    }

    public function items() {
        return $this->hasMany(OrderItems::class, 'order_id');
    }

    public function user() {
        return $this->belongsTo(User::class);
    }

    protected $casts = [
    'order_date' => 'datetime',
];

}