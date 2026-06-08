<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PurchaseHistory extends Model
{
    protected $fillable = [
        'user_id',
        'template_name',
        'finished_at',
        'total',
        'sectors_summary',
    ];

    protected function casts(): array
    {
        return [
            'finished_at' => 'datetime',
            'total' => 'decimal:2',
            'sectors_summary' => 'array',
        ];
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
