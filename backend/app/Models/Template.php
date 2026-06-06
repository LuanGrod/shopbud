<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\Sortable;
use Database\Factories\TemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    /** @use HasFactory<TemplateFactory> */
    use HasFactory, Searchable, Sortable;

    protected $fillable = [
        'name',
        'user_id',
    ];

    protected static function booted(): void
    {
        static::deleting(function (Template $template): void {
            $template->sectors()->get()->each->delete();
            $template->sharedTemplates()->delete();
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function sectors(): HasMany
    {
        return $this->hasMany(Sector::class)->orderBy('order');
    }

    public function sharedTemplates(): HasMany
    {
        return $this->hasMany(SharedTemplate::class);
    }

    protected function getSearchableFields(): array
    {
        return ['name'];
    }

    protected function getSortableFields(): array
    {
        return ['id', 'name'];
    }
}
