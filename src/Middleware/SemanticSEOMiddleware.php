<?php

namespace NoelDeMartin\SemanticSEO\Middleware;

use Closure;
use NoelDeMartin\SemanticSEO\Support\Facades\SemanticSEO;

class SemanticSEOMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $action)
    {
        switch ($action) {
            case 'hide':
                SemanticSEO::hide();
                break;
        }

        return $next($request);
    }
}
