<?php

namespace Modules\Admin\Casts;

use Illuminate\Contracts\Database\Eloquent\CastsAttributes;

class DelimitedCast implements CastsAttributes
{
    /**
     * Cast the given value.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function get($model, string $key, $value, array $attributes)
    {
        return $value;
    }

    /**
     * Prepare the given value for storage.
     *
     * @param  array<string, mixed>  $attributes
     */
    public function set($model, string $key, $value, array $attributes)
    {
        $filteredKeywords = array_filter($value, fn($tag) => !is_null($tag) && trim($tag) !== '');
        return !empty($filteredKeywords) ? implode(',', $filteredKeywords) : null;
    }
}
