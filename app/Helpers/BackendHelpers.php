<?php

namespace App\Helpers;

use App\Models\AdminSettings;
use Illuminate\Http\Request;

class BackendHelpers
{
    /**
     * Retrieve the formatted value for a given key from the settings.
     *
     * @param string $key The key to search for in the settings table.
     * @return mixed|null The formatted value if found, otherwise null.
     */
    public static function getValueByKey($key)
    {
        return optional(AdminSettings::where('key', $key)->first())->formatted_value ?? null;
    }

    /**
     * Check if the first order column in the request is set to zero.
     *
     * @param Request $request The incoming HTTP request.
     * @return bool Returns true if the first order column is zero; otherwise, false.
     */
    public static function isOrderColumnZero(Request $request): bool
    {
        return ($request->order[0]['column'] ?? null) == 0;
    }
}
