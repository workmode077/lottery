<?php

namespace App\Http\Controllers;


use App\Traits\LocalizationTrait;
use App\Traits\MetaDataTrait;
use App\Traits\ResponseHandlerTrait;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Str;


abstract class Controller
{
    use ResponseHandlerTrait,LocalizationTrait, MetaDataTrait;


    protected function getCurrentRouteName()
    {
        return Route::currentRouteName();
    }
    public function getBaseRouteName()
    {
        $routeName = Route::currentRouteName();
        return explode('.', $routeName)[0];
    }

    public function getBaseRouteNameSub()
    {
        $routeName = Route::currentRouteName(); // e.g. vehicle.model-year.index
        $parts = explode('.', $routeName);
        array_pop($parts); // remove the last part (e.g. 'index')
        return implode('.', $parts); // join remaining parts back with '.'
    }
}
