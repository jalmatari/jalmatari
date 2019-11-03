<?php
namespace Jalmatari\DB\Scopes;

use Illuminate\Database\Eloquent\Scope;

class JalmatariScope implements Scope
{


    /**
     * Scope a query to only include active recorders.
     *
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }
}
