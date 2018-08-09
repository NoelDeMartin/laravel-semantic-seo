<?php

namespace Testing\Unit;

use Testing\TestCase;
use NoelDeMartin\SemanticSEO\Types\Thing;
use NoelDeMartin\SemanticSEO\Types\WebSite;
use NoelDeMartin\SemanticSEO\Support\Facades\SemanticSEO;

class TypesTests extends TestCase
{
    public function test_thing()
    {
        $name = $this->faker->name;
        $description = $this->faker->sentence;

        SemanticSEO::is(Thing::class)
            ->name($name)
            ->description($description);

        $html = SemanticSEO::render();

        $this->assertContains(
            '<script type="application/ld+json">' .
                json_encode([
                    '@context' => 'http://schema.org',
                    'name' => $name,
                    'description' => $description,
                    '@type' => 'Thing',
                ]) .
            '</script>',
            $html
        );
        $this->assertContains("<title>$name</title>", $html);
        $this->assertContains("<meta name=\"description\" content=\"$description\" />", $html);
    }

    public function test_web_site()
    {
        $url = $this->faker->url;
        $name = $this->faker->word;
        $headline = $this->faker->sentence;
        $description = $this->faker->sentence;

        SemanticSEO::website()
            ->url($url)
            ->name($name)
            ->headline($headline)
            ->description($description);

        $html = SemanticSEO::render();

        $this->assertContains(
            '<script type="application/ld+json">' .
                json_encode([
                    '@context' => 'http://schema.org',
                    'url' => $url,
                    'name' => $name,
                    'headline' => $headline,
                    'description' => $description,
                    '@type' => 'WebSite',
                ]) .
            '</script>',
            $html
        );
        $this->assertContains("<title>$headline</title>", $html);
        $this->assertContains("<link rel=\"canonical\" href=\"$url\" />", $html);
        $this->assertContains("<meta name=\"description\" content=\"$description\" />", $html);
    }

    public function test_disable_automatically_filled_meta()
    {
        $name = $this->faker->name;

        SemanticSEO::meta('title', null);
        SemanticSEO::is(Thing::class)->name($name);

        $this->assertNotContains("<title>$name</title>", SemanticSEO::render());
    }

    public function test_multiple_types()
    {
        SemanticSEO::is(Thing::class);
        SemanticSEO::is(WebSite::class);

        $this->assertContains(
            '<script type="application/ld+json">' .
                json_encode([
                    '@context' => 'http://schema.org',
                    '@graph' => [
                        [
                            '@type' => 'Thing',
                        ],
                        [
                            '@type' => 'WebSite',
                        ],
                    ],
                ]) .
            '</script>',
            SemanticSEO::render()
        );
    }
}
