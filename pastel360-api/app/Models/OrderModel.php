<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class OrderModel extends Model
{
    use SoftDeletes, HasFactory;
    protected $table = 'orders';

    protected $fillable = [
        'product_id',
        'client_id',
        'quantity',
        'unit_value',
        'total_value',
        'status',
        'notes'
    ];

    protected $casts = [
        'quantity' => 'integer',
        'unit_price' => 'decimal:2',
        'total_price' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($order) {
            if (empty($order->total_value) && $order->quantity && $order->unit_value) {
                $order->total_value = $order->quantity * $order->unit_value;
            }
        });

        static::updating(function ($order) {
            if ($order->isDirty(['quantity', 'unit_value'])) {
                $order->total_value = $order->quantity * $order->unit_value;
            }
        });
    }

    public function client(): BelongsTo
    {
        return $this->belongsTo(ClientModel::class, 'client_id');
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(ProductModel::class, 'product_id');
    }
}
