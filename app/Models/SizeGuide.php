<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SizeGuide extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'scope_type',
        'scope_id',
        'gender',
        'unit',
        'table_schema',
        'rows',
        'is_active',
    ];

    protected $casts = [
        'table_schema' => 'array',
        'rows' => 'array',
        'is_active' => 'boolean',
    ];
}
