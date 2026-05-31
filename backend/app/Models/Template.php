<?php

namespace App\Models;

use App\Traits\Searchable;
use App\Traits\Sortable;
use Database\Factories\TemplateFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    /** @use HasFactory<TemplateFactory> */
    use HasFactory, Searchable, Sortable;

    protected $fillable = [
        'name',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
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
