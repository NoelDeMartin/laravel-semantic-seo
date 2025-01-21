<?php

namespace NoelDeMartin\SemanticSEO\Middleware;

use Closure;
use Illuminate\Http\Request;
use NoelDeMartin\SemanticSEO\Support\Facades\SemanticSEO;

class SemanticSEOMiddleware
{
    public function handle(Request $request, Closure $next, string $action): mixed
    {
        switch ($action) {
            case 'hide':
                SemanticSEO::hide();
                break;
        }

        return $next($request);
    }
}
