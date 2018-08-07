<?php

namespace Testing;

use Faker\Factory as Faker;
use Illuminate\Support\Facades\Facade;
use NoelDeMartin\SemanticSEO\SemanticSEO;
use PHPUnit\Framework\TestCase as BaseTestCase;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        $this->faker = Faker::create();
        Facade::setFacadeApplication([
            'semantic-seo' => new SemanticSEO,
        ]);
    }
}
