<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Sector extends Model
{
    protected $fillable = [
        'name',
        'order',
        'template_id',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Sector $sector): void {
            $sector->products()->delete();
        });
    }

    public function template(): BelongsTo
    {
        return $this->belongsTo(Template::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class)->orderBy('created_at')->orderBy('id');
    }
}
