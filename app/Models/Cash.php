<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Cash extends Model
{
    protected $table = 'cash';
    
    protected $fillable = ['name', 'amount'];

     public function transactions()
    {
        return $this->hasMany(Transactions::class, 'cash_id');
    }

    public function orders()
    {
        return $this->hasMany(Orders::class, 'cash_id');
    }

    public function purchases()
    {
        return $this->hasMany(Purchases::class, 'cash_id');
    }
}
