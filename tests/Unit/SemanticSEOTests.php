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

    public function test_render_custom_meta()
    {
        $content = $this->faker->sentence;
        $name = $this->faker->word;
        $nameAttribute = $this->faker->word;

        SemanticSEO::meta([
            $name => compact('nameAttribute', 'content'),
        ]);

        $this->assertEquals(
            "<meta $nameAttribute=\"$name\" content=\"$content\" />",
            SemanticSEO::render()
        );
    }

    public function test_hide()
    {
        SemanticSEO::hide();

        $this->assertEquals(
            '<meta name="robots" content="noindex, nofollow" />',
            SemanticSEO::render()
        );
    }
}
