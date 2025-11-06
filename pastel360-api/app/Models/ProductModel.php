<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ProductModel extends Model
{
    use SoftDeletes;
    protected $table = 'products';

    protected $fillable = [
        'name',
        'description',
        'price',
        'photo',
        'stock',
        'sku',
        'enable'
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'stock' => 'integer',
        'enable' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];
}
