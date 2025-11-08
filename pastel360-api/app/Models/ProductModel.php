<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductModel extends Model
{
    use SoftDeletes, HasFactory;
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

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($product) {
            if (empty($product->sku)) {
                $product->sku = 'TEMP-SKU';
            }
        });

        static::created(function ($product) {
            if ($product->sku === 'TEMP-SKU') {
                $namePart = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $product->name), 0, 10));
                $product->sku = 'PROD-' . $namePart . '-' . str_pad($product->id, 3, '0', STR_PAD_LEFT);
                $product->saveQuietly();
            }
        });
    }
}
