<?php

namespace Testing\Unit;

use Testing\TestCase;
use NoelDeMartin\SemanticSEO\Support\Facades\SemanticSEO;

class SemanticSEOTests extends TestCase
{
    public function test_render_title()
    {
        $title = $this->faker->sentence;

        SemanticSEO::title($title);

        $this->assertEquals("<title>$title</title>", SemanticSEO::render());
    }

    public function test_render_description()
    {
        $description = $this->faker->sentence;

        SemanticSEO::description($description);

        $this->assertEquals(
            "<meta name=\"description\" content=\"$description\" />",
            SemanticSEO::render()
        );
    }
}
