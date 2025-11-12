<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    use HasFactory;

    protected $fillable = [
        'date',
        'transaction_category_id',
        'cash_id',
        'name',
        'amount',
        'description',
        'image'
    ];

    public function category()
    {
        return $this->belongsTo(TransactionCategories::class, 'transaction_category_id');
    }

    public function cash()
    {
        return $this->belongsTo(Cash::class, 'cash_id');
    }

    public function transactionCategory()
    {
        return $this->belongsTo(TransactionCategories::class, 'transaction_category_id');
    }
}
