<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Sortable
{
    abstract protected function getSortableFields(): array;

    public function scopeWithSort(Builder $query, ?Request $request = null): Builder
    {
        $request = $request ?? request();

        $sort = $request->string('sort', 'id')->toString();
        $direction = $request->string('direction', 'desc')->toString();

        if (in_array($sort, $this->getSortableFields())) {
            $query->orderBy($sort, in_array($direction, ['asc', 'desc']) ? $direction : 'desc');
        }

        return $query;
    }
}
