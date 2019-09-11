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

        $this->assertStringContainsString("<title>{$title}{$suffix}</title>", SemanticSEO::render());
    }

    public function test_render_description()
    {
        $description = $this->faker->sentence;

        SemanticSEO::description($description);

        $this->assertStringContainsString(
            "<meta name=\"description\" content=\"$description\">",
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

        $this->assertStringContainsString(
            "<meta name=\"$name\" property=\"$property\" content=\"$content\">",
            SemanticSEO::render()
        );
    }

    public function test_render_rss()
    {
        $url = $this->faker->url;
        $title = $this->faker->sentence;

        SemanticSEO::rss($url, $title);

        $this->assertStringContainsString(
            "<link rel=\"alternate\" type=\"application/rss+xml\" title=\"$title\" href=\"$url\">",
            SemanticSEO::render()
        );
    }

    public function test_render_sitemap()
    {
        $url = $this->faker->url;
        $title = $this->faker->sentence;

        SemanticSEO::sitemap($url, $title);

        $this->assertStringContainsString(
            "<link rel=\"sitemap\" type=\"application/xml\" title=\"$title\" href=\"$url\">",
            SemanticSEO::render()
        );
    }

    public function test_render_hide()
    {
        SemanticSEO::hide();

        $this->assertStringContainsString(
            '<meta name="robots" content="noindex, nofollow">',
            SemanticSEO::render()
        );
    }

    public function test_render_canonical()
    {
        $url = $this->faker->url;

        SemanticSEO::canonical($url);

        $this->assertStringContainsString(
            "<link rel=\"canonical\" href=\"$url\">",
            SemanticSEO::render()
        );
    }

    public function test_render_canonical_default()
    {
        $url = URL::current();

        $this->assertStringContainsString(
            "<link rel=\"canonical\" href=\"$url\">",
            SemanticSEO::render()
        );
    }

    public function test_render_canonical_disabled()
    {
        SemanticSEO::canonical(false);

        $this->assertStringNotContainsString('<link rel="canonical" href="', SemanticSEO::render());
    }

    public function test_render_twitter()
    {
        $card = 'summary';
        $creator = '@' . $this->faker->word;

        SemanticSEO::twitter(compact('card', 'creator'));

        $html = SemanticSEO::render();
        $this->assertStringContainsString("<meta name=\"twitter:card\" content=\"$card\">", $html);
        $this->assertStringContainsString("<meta name=\"twitter:creator\" content=\"$creator\">", $html);
    }

    public function test_render_open_graph()
    {
        $title = $this->faker->sentence;
        $url = $this->faker->url;

        SemanticSEO::openGraph(compact('title', 'url'));

        $html = SemanticSEO::render();
        $this->assertStringContainsString("<meta property=\"og:title\" content=\"$title\">", $html);
        $this->assertStringContainsString("<meta property=\"og:url\" content=\"$url\">", $html);
    }
}
