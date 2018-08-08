<?php

namespace Testing\Unit;

use Testing\TestCase;
use NoelDeMartin\SemanticSEO\Support\Facades\SemanticSEO;

class SemanticSEOTests extends TestCase
{
    public function test_render_title()
    {
        $title = $this->faker->sentence;
        $suffix = $this->faker->word;

        SemanticSEO::title($title)->titleSuffix($suffix);

        $this->assertContains("<title>{$title}{$suffix}</title>", SemanticSEO::render());
    }

    public function test_render_description()
    {
        $description = $this->faker->sentence;

        SemanticSEO::description($description);

        $this->assertContains(
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

        $this->assertContains(
            "<meta $nameAttribute=\"$name\" content=\"$content\" />",
            SemanticSEO::render()
        );
    }

    public function test_render_hide()
    {
        SemanticSEO::hide();

        $this->assertContains(
            '<meta name="robots" content="noindex, nofollow" />',
            SemanticSEO::render()
        );
    }

    public function test_render_canonical()
    {
        $url = $this->faker->url;

        SemanticSEO::canonical($url);

        $this->assertContains(
            "<link rel=\"canonical\" href=\"$url\" />",
            SemanticSEO::render()
        );
    }

    public function test_render_canonical_default()
    {
        $url = $this->url;

        $this->assertContains(
            "<link rel=\"canonical\" href=\"{$url}\" />",
            SemanticSEO::render()
        );
    }

    public function test_render_canonical_disabled()
    {
        SemanticSEO::canonical(false);

        $this->assertNotContains('<link rel="canonical" href="', SemanticSEO::render());
    }
}
