<?php

namespace NoelDeMartin\SemanticSEO;

use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;

class SemanticSEOServiceProvider extends ServiceProvider
{
    /**
     * Boot Semantic SEO extensions.
     *
     * @return void
     */
    public function boot()
    {
        Blade::directive('semanticSEO', function () {
            return "<?php echo app('semantic-seo')->render(); ?>";
        });
    }

    /**
     * Register Semantic SEO services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('semantic-seo', SemanticSEO::class);
    }
}
