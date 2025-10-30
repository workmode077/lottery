<?php

namespace App\Traits;

trait QueryScope
{
    public function scopeActive($query)
    {
        return $query->where('status', true);
    }

    public function scopeSort($query)
    {
        return $query->orderBy('sort_order');
    }

    public function scopeDate($query)
    {
        return $query->orderBy('published_on');
    }

    public function scopeWithMedia($query)
    {
        return $query->with('media');
    }
}
