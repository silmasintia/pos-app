<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class LogHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'table_name',
        'entity_id',
        'action',
        'user',
        'old_data',
        'new_data',
        'timestamp',
    ];

    protected $casts = [
        'old_data' => 'array',
        'new_data' => 'array',
        'timestamp' => 'datetime',
    ];

    public function userRelation()
    {
        return $this->belongsTo(User::class, 'user');
    }

}