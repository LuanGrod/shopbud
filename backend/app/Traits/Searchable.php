<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Searchable
{
    abstract protected function getSearchableFields(): array;

    public function scopeWithSearch(Builder $query, ?Request $request = null): Builder
    {
        $request = $request ?? request();

        if (! $search = $request->string('search')->toString()) {
            return $query;
        }

        $searchable = $this->getSearchableFields();

        if (empty($searchable)) {
            return $query;
        }

        $query->where(function ($q) use ($search, $searchable) {
            foreach ($searchable as $field) {
                $q->orWhere($field, 'like', "%{$search}%");
            }
        });

        return $query;
    }
}
