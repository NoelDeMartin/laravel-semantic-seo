<?php

namespace NoelDeMartin\SemanticSEO\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \NoelDeMartin\SemanticSEO\SemanticSEO
 */
class SemanticSEO extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'semantic-seo';
    }
}
