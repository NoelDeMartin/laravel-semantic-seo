<?php

namespace Testing\Unit;

use Testing\TestCase;
use NoelDeMartin\SemanticSEO\Support\Facades\SemanticSEO;

class SemanticSEOTests extends TestCase
{
    public function test_render()
    {
        $title = $this->faker->sentence;

        SemanticSEO::title($title);

        $this->assertEquals("<title>$title</title>", SemanticSEO::render());
    }
}
