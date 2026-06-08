<?php

namespace App\Models;

use Database\Factories\ShoppingSessionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ShoppingSession extends Model
{
    /** @use HasFactory<ShoppingSessionFactory> */
    use HasFactory;

    protected $fillable = [
        'user_id',
        'template_id',
        'status',
        'snapshot',
        'expires_at',
    ];

    protected function casts(): array
    {
        return [
            'snapshot' => 'array',
            'expires_at' => 'datetime',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function shoppingItems(): HasMany
    {
        return $this->hasMany(ShoppingItem::class, 'session_id');
    }
}
