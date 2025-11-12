<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProfitLoss extends Model
{
    use HasFactory;

    protected $table = 'profit_loss';

    protected $fillable = [
        'cash_id',
        'transaction_id',
        'order_id',
        'purchase_id',
        'date',
        'category',
        'amount',
    ];

    protected $dates = [
        'date',
    ];

    public function cash()
    {
        return $this->belongsTo(Cash::class, 'cash_id');
    }

    public function transaction()
    {
        return $this->belongsTo(Transactions::class, 'transaction_id');
    }

    public function order()
    {
        return $this->belongsTo(Orders::class, 'order_id');
    }

    public function purchase()
    {
        return $this->belongsTo(Purchases::class, 'purchase_id');
    }
}
