<?php

namespace Testing;

use Faker\Factory as Faker;
use NoelDeMartin\SemanticSEO\SemanticSEO;
use PHPUnit\Framework\TestCase as BaseTestCase;
use NoelDeMartin\SemanticSEO\Support\Facades\SemanticSEO as Facade;

class TestCase extends BaseTestCase
{
    public function setUp()
    {
        $this->faker = Faker::create();
        Facade::swap(new SemanticSEO);
    }
}
