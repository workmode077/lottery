<?php

use App\Providers\AppServiceProvider;
use Barryvdh\Debugbar\ServiceProvider;
use Yajra\DataTables\DataTablesServiceProvider;

return [
    AppServiceProvider::class,
    DataTablesServiceProvider::class,
    ServiceProvider::class,
];
