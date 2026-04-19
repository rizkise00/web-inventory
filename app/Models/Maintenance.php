<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = [
        'item_name',
        'description',
        'date',
        'status',
        
    ];

    protected $casts = [
        'date' => 'date',
        
    ];
}