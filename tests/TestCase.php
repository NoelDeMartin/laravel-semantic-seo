<?php

namespace Testing;

use Faker\Factory as Faker;
use Illuminate\Container\Container;
use Illuminate\Routing\UrlGenerator;
use Illuminate\Support\Facades\Facade;
use Mockery;
use NoelDeMartin\SemanticSEO\SemanticSEO;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    protected $app;

    protected $faker;

    protected function setUp(): void
    {
        $url = Mockery::mock(UrlGenerator::class);
        $url->shouldReceive('current')->andReturn('http://example.com');

        $this->faker = Faker::create();
        $this->app = new Container;

        $this->app->instance('url', $url);
        $this->app->instance('app', $this->app);
        $this->app->singleton('semantic-seo', SemanticSEO::class);

        Facade::setFacadeApplication($this->app);
    }

    protected function tearDown(): void
    {
        Mockery::close();

        Facade::clearResolvedInstance('semantic-seo');
        Facade::clearResolvedInstance('url');
        Facade::clearResolvedInstance('app');
    }
}
