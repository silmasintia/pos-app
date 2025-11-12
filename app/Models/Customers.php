<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customers extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'customer_category_id',
    ];

    public function orders()
    {
        return $this->hasMany(Orders::class, 'customer_id');
    }

    public function category()
    {
        return $this->belongsTo(CustomerCategories::class, 'customer_category_id');
    }
}
