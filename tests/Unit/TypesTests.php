<?php

namespace Testing\Unit;

use Testing\TestCase;
use NoelDeMartin\SemanticSEO\Types\Thing;
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
                    'name' => $name,
                    'description' => $description,
                    '@context' => 'http://schema.org',
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
                    'url' => $url,
                    'name' => $name,
                    'headline' => $headline,
                    'description' => $description,
                    '@context' => 'http://schema.org',
                    '@type' => 'WebSite',
                ]) .
            '</script>',
            $html
        );
        $this->assertContains("<title>$headline</title>", $html);
        $this->assertContains("<link rel=\"canonical\" href=\"$url\" />", $html);
        $this->assertContains("<meta name=\"description\" content=\"$description\" />", $html);
    }
}
