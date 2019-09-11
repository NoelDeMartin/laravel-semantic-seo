<?php

namespace Testing;

use Mockery;
use Faker\Factory as Faker;
use Illuminate\Container\Container;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Facade;
use NoelDeMartin\SemanticSEO\SemanticSEO;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $app;

    public function setUp(): void
    {
        $this->faker = Faker::create();

        $this->app = new Container;

        $this->app->singleton('semantic-seo', SemanticSEO::class);
        $this->app->singleton('url', UrlGenerator::class);
        $this->app->instance('app', $this->app);

        Facade::setFacadeApplication($this->app);
    }

    public function tearDown(): void
    {
        Mockery::close();

        Facade::clearResolvedInstance('semantic-seo');
        Facade::clearResolvedInstance('url');
        Facade::clearResolvedInstance('app');
    }
}
