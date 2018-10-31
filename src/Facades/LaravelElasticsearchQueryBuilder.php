<?php

namespace Shisun\LaravelElasticsearchQueryBuilder\Facades;

use Illuminate\Support\Facades\Facade;

class LaravelElasticsearchQueryBuilder extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'laravel-elastic-search-query-builder';
    }
}
