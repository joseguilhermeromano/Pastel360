<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItemModel extends Model
{
    use SoftDeletes, HasFactory;

    protected $table = 'order_items';

    protected $fillable = [
        'order_id',
        'product_id',
        'quantity',
        'unit_value',
        'total_value'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_value' => 'decimal:2',
        'total_value' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::saving(function ($item) {
            $item->total_value = $item->quantity * $item->unit_value;
        });
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(OrderModel::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class);
    }
}
