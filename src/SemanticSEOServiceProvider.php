<?php

namespace NoelDeMartin\SemanticSEO;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use NoelDeMartin\SemanticSEO\Middleware\SemanticSEOMiddleware;

class SemanticSEOServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Blade::directive('semanticSEO', function () {
            return "<?php echo app('semantic-seo')->render(); ?>";
        });
    }

    public function register(): void
    {
        $this->app->singleton('semantic-seo', SemanticSEO::class);
        $this->app['router']->aliasMiddleware('semantic-seo', SemanticSEOMiddleware::class);
    }
}
