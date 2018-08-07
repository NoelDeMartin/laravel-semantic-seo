<?php

namespace NoelDeMartin\SemanticSEO;

use Illuminate\Support\ServiceProvider;

class SemanticSEOServiceProvider extends ServiceProvider
{
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
