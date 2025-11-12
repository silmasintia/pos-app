<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransactionCategories extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 
        'parent_type', 
        'description'
    ];

    public function transactions()
    {
        return $this->hasMany(Transactions::class);
    }
}
