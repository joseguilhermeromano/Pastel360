<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

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
        'category' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime'
    ];

    protected static function boot()
    {
        parent::boot();

        static::created(function ($product) {
            if (!empty($product->name)) {

                $newSku = $product->getSkuAttribute(null);
                $path = 'products/';

                if ($product->photo && Storage::disk('public')->exists('products/' . $product->photo)) {
                    $extension = pathinfo($product->photo, PATHINFO_EXTENSION);
                    $newPhoto = $newSku . '.' . $extension;

                    Storage::disk('public')->move($path . $product->photo, $path . $newPhoto);
                    $product->photo = $newPhoto;
                }

                $product->sku = $newSku;
                $product->saveQuietly();
            }
        });
    }

    public function getSkuAttribute($value)
    {
        if (empty($value) && !empty($this->name)) {
            $name = preg_replace('/\s+de\s+/i', ' ', $this->name);
            $namePart = strtoupper(substr(preg_replace('/[^A-Za-z0-9]/', '', $name), 0, 17));
            return 'PROD-' . $namePart . '-' . strtoupper(Str::random(8));
        }

        return $value;
    }
}
