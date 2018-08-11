<?php

namespace Testing\Unit;

use DateTime;
use Testing\TestCase;
use Testing\Stubs\JohnDoe;
use NoelDeMartin\SemanticSEO\Types\Thing;
use NoelDeMartin\SemanticSEO\Types\Person;
use NoelDeMartin\SemanticSEO\Types\WebSite;
use NoelDeMartin\SemanticSEO\Types\ImageObject;
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
        $date = $this->faker->datetime();

        SemanticSEO::website()
            ->url($url)
            ->name($name)
            ->headline($headline)
            ->description($description)
            ->dateCreated($date);

        $html = SemanticSEO::render();

        $this->assertContains(
            '<script type="application/ld+json">' .
                json_encode([
                    '@context' => 'http://schema.org',
                    'url' => $url,
                    'name' => $name,
                    'headline' => $headline,
                    'description' => $description,
                    'dateCreated' => $date->format(DateTime::ISO8601),
                    '@type' => 'WebSite',
                ]) .
            '</script>',
            $html
        );
        $this->assertContains("<title>$headline</title>", $html);
        $this->assertContains("<link rel=\"canonical\" href=\"$url\" />", $html);
        $this->assertContains("<meta name=\"description\" content=\"$description\" />", $html);
    }

    public function test_article()
    {
        $wordCount = random_int(50, 1000);
        $publishedAt = $this->faker->datetime();

        SemanticSEO::article()
            ->wordCount($wordCount)
            ->datePublished($publishedAt)
            ->keywords('foo, bar')
            ->author(JohnDoe::class);

        $html = SemanticSEO::render();

        $this->assertContains(
            '<script type="application/ld+json">' .
                json_encode([
                    '@context' => 'http://schema.org',
                    'wordCount' => $wordCount,
                    'datePublished' => $publishedAt->format(DateTime::ISO8601),
                    'keywords' => 'foo, bar',
                    'author' => [
                        'name' => 'John Doe',
                        'sameAs' => [
                            'https://twitter.com/JohnDoe',
                            'https://github.com/JohnDoe',
                        ],
                        '@type' => 'Person',
                    ],
                    '@type' => 'Article',
                ]) .
            '</script>',
            $html
        );
        $this->assertContains(
            "<meta property=\"article:published_time\" content=\"{$publishedAt->format(DateTime::ISO8601)}\" />",
            $html
        );
        $this->assertContains("<meta property=\"article:author\" content=\"John Doe\" />", $html);
        $this->assertContains("<meta property=\"article:tag\" content=\"foo\" />", $html);
        $this->assertContains("<meta property=\"article:tag\" content=\"bar\" />", $html);
    }

    public function test_custom_type()
    {
        SemanticSEO::is(JohnDoe::class);

        $this->assertContains(
            '<script type="application/ld+json">' .
                json_encode([
                    '@context' => 'http://schema.org',
                    'name' => 'John Doe',
                    'sameAs' => [
                        'https://twitter.com/JohnDoe',
                        'https://github.com/JohnDoe',
                    ],
                    '@type' => 'Person',
                ]) .
            '</script>',
            SemanticSEO::render()
        );
    }

    public function test_twitter_handle()
    {
        $twitterHandle = $this->faker->word;

        $creator = (new Person)->sameAs('https://twitter.com/' . $twitterHandle);
        SemanticSEO::website()->creator($creator);

        $this->assertContains(
            "<meta name=\"twitter:site\" content=\"@$twitterHandle\" />",
            SemanticSEO::render()
        );
    }

    public function test_fill_socials()
    {
        $url = $this->faker->url;
        $description = $this->faker->sentence;

        $image = (new ImageObject)
            ->url($url)
            ->description($description);

        SemanticSEO::website()->image($image);

        $html = SemanticSEO::render();

        $this->assertContains("<meta property=\"og:image\" content=\"$url\" />", $html);
        $this->assertContains("<meta property=\"og:image:alt\" content=\"$description\" />", $html);
        $this->assertContains("<meta name=\"twitter:image\" content=\"$url\" />", $html);
        $this->assertContains("<meta name=\"twitter:image:alt\" content=\"$description\" />", $html);
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
