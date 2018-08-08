<?php

namespace Testing\Unit;

use Testing\TestCase;
use Illuminate\Support\Facades\URL;
use NoelDeMartin\SemanticSEO\Support\Facades\SemanticSEO;

class MetaTagsTests extends TestCase
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
        $property = $this->faker->word;

        SemanticSEO::meta([
            $name => compact('property', 'content'),
        ]);

        $this->assertContains(
            "<meta name=\"$name\" property=\"$property\" content=\"$content\" />",
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
        $url = URL::current();

        $this->assertContains(
            "<link rel=\"canonical\" href=\"$url\" />",
            SemanticSEO::render()
        );
    }

    public function test_render_canonical_disabled()
    {
        SemanticSEO::canonical(false);

        $this->assertNotContains('<link rel="canonical" href="', SemanticSEO::render());
    }

    public function test_render_twitter()
    {
        $card = 'summary';
        $creator = '@' . $this->faker->word;

        SemanticSEO::twitter(compact('card', 'creator'));

        $html = SemanticSEO::render();
        $this->assertContains("<meta name=\"twitter:card\" content=\"$card\" />", $html);
        $this->assertContains("<meta name=\"twitter:creator\" content=\"$creator\" />", $html);
    }

    public function test_render_open_graph()
    {
        $title = $this->faker->sentence;
        $url = $this->faker->url;

        SemanticSEO::openGraph(compact('title', 'url'));

        $html = SemanticSEO::render();
        $this->assertContains("<meta property=\"og:title\" content=\"$title\" />", $html);
        $this->assertContains("<meta property=\"og:url\" content=\"$url\" />", $html);
    }
}
