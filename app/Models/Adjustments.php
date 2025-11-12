<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Adjustments extends Model
{
    use HasFactory;

    protected $fillable = [
        'adjustment_number',
        'adjustment_date',
        'description',
        'total',
        'image'
    ];

    public function details()
    {
        return $this->hasMany(AdjustmentDetails::class, 'adjustment_id');
    }
}
