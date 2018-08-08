<?php

namespace Testing;

use Mockery;
use Faker\Factory as Faker;
use NoelDeMartin\SemanticSEO\SemanticSEO;
use PHPUnit\Framework\TestCase as BaseTestCase;
use NoelDeMartin\SemanticSEO\Support\Facades\SemanticSEO as SemanticSEOFacade;
use Illuminate\Support\Facades\URL;

class TestCase extends BaseTestCase
{
    protected $url;

    public function setUp()
    {
        $this->faker = Faker::create();
        $this->setupFacades();
    }

    public function tearDown()
    {
        Mockery::close();
    }

    protected function setupFacades()
    {
        SemanticSEOFacade::swap(new SemanticSEO);
        URL::swap($urlMock = Mockery::mock());

        $urlMock->shouldReceive('current')->andReturn($this->url = $this->faker->url);
    }
}
