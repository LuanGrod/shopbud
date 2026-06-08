<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ShoppingItem extends Model
{
    protected $fillable = [
        'session_id',
        'sector_name',
        'product_name',
        'price',
        'quantity',
        'extra',
    ];

    protected function casts(): array
    {
        return [
            'price' => 'decimal:2',
            'quantity' => 'integer',
            'extra' => 'boolean',
        ];
    }

    public function shoppingSession(): BelongsTo
    {
        return $this->belongsTo(ShoppingSession::class, 'session_id');
    }
}
