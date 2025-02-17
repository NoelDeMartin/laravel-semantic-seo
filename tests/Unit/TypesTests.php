<?php

namespace Testing\Unit;

use Illuminate\Support\Carbon;
use NoelDeMartin\SemanticSEO\Support\Facades\SemanticSEO;
use NoelDeMartin\SemanticSEO\Types\CreativeWork;
use NoelDeMartin\SemanticSEO\Types\ImageObject;
use NoelDeMartin\SemanticSEO\Types\Person;
use NoelDeMartin\SemanticSEO\Types\Thing;
use NoelDeMartin\SemanticSEO\Types\WebSite;
use Testing\Stubs\JohnDoe;
use Testing\TestCase;

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

        $this->assertStringContainsString(
            '<script type="application/ld+json">'.
                json_encode([
                    '@context' => 'http://schema.org',
                    'name' => $name,
                    'description' => $description,
                    '@type' => 'Thing',
                ]).
            '</script>',
            $html
        );
        $this->assertStringContainsString("<title>{$name}</title>", $html);
        $this->assertStringContainsString("<meta name=\"description\" content=\"{$description}\">", $html);
    }

    public function test_web_site()
    {
        $url = $this->faker->url;
        $name = $this->faker->word;
        $headline = $this->faker->sentence;
        $description = $this->faker->sentence;
        $date = Carbon::instance($this->faker->datetime());

        SemanticSEO::website()
            ->url($url)
            ->name($name)
            ->headline($headline)
            ->description($description)
            ->dateCreated($date);

        $html = SemanticSEO::render();

        $this->assertStringContainsString(
            '<script type="application/ld+json">'.
                json_encode([
                    '@context' => 'http://schema.org',
                    'url' => $url,
                    'name' => $name,
                    'headline' => $headline,
                    'description' => $description,
                    'dateCreated' => $date->toISO8601String(),
                    '@type' => 'WebSite',
                ]).
            '</script>',
            $html
        );
        $this->assertStringContainsString("<title>{$headline}</title>", $html);
        $this->assertStringContainsString("<link rel=\"canonical\" href=\"{$url}\">", $html);
        $this->assertStringContainsString("<meta name=\"description\" content=\"{$description}\">", $html);
    }

    public function test_article()
    {
        $wordCount = random_int(50, 1000);
        $publishedAt = Carbon::instance($this->faker->datetime());

        SemanticSEO::article()
            ->wordCount($wordCount)
            ->datePublished($publishedAt)
            ->keywords('foo, bar')
            ->author(JohnDoe::class);

        $html = SemanticSEO::render();

        $this->assertStringContainsString(
            '<script type="application/ld+json">'.
                json_encode([
                    '@context' => 'http://schema.org',
                    'wordCount' => $wordCount,
                    'datePublished' => $publishedAt->toISO8601String(),
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
                ]).
            '</script>',
            $html
        );
        $this->assertStringContainsString(
            "<meta property=\"article:published_time\" content=\"{$publishedAt->toISO8601String()}\">",
            $html
        );
        $this->assertStringContainsString('<meta property="article:author" content="John Doe">', $html);
        $this->assertStringContainsString('<meta property="article:tag" content="foo">', $html);
        $this->assertStringContainsString('<meta property="article:tag" content="bar">', $html);
    }

    public function test_collection_page()
    {
        $title = $this->faker->sentence;
        $name = $this->faker->name;

        $creativeWork = (new CreativeWork)->name($title);
        $webSite = (new WebSite)->name($name);

        SemanticSEO::collection()
            ->hasPart([
                $creativeWork,
                $webSite,
            ]);

        $this->assertStringContainsString(
            '<script type="application/ld+json">'.
                json_encode([
                    '@context' => 'http://schema.org',
                    'hasPart' => [
                        [
                            'name' => $title,
                            '@type' => 'CreativeWork',
                        ],
                        [
                            'name' => $name,
                            '@type' => 'WebSite',
                        ],
                    ],
                    '@type' => 'CollectionPage',
                ]).
            '</script>',
            SemanticSEO::render()
        );
    }

    public function test_custom_type()
    {
        SemanticSEO::is(JohnDoe::class);

        $this->assertStringContainsString(
            '<script type="application/ld+json">'.
                json_encode([
                    '@context' => 'http://schema.org',
                    'name' => 'John Doe',
                    'sameAs' => [
                        'https://twitter.com/JohnDoe',
                        'https://github.com/JohnDoe',
                    ],
                    '@type' => 'Person',
                ]).
            '</script>',
            SemanticSEO::render()
        );
    }

    public function test_twitter_handle()
    {
        $twitterHandle = $this->faker->word;

        $creator = (new Person)->sameAs('https://twitter.com/'.$twitterHandle);
        SemanticSEO::website()->creator($creator);

        $this->assertStringContainsString(
            "<meta name=\"twitter:site\" content=\"@{$twitterHandle}\">",
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

        $this->assertStringContainsString("<meta property=\"og:image\" content=\"{$url}\">", $html);
        $this->assertStringContainsString("<meta property=\"og:image:alt\" content=\"{$description}\">", $html);
        $this->assertStringContainsString("<meta name=\"twitter:image\" content=\"{$url}\">", $html);
        $this->assertStringContainsString("<meta name=\"twitter:image:alt\" content=\"{$description}\">", $html);
    }

    public function test_disable_automatically_filled_meta()
    {
        $name = $this->faker->name;

        SemanticSEO::meta('title', null);
        SemanticSEO::is(Thing::class)->name($name);

        $this->assertStringNotContainsString("<title>{$name}</title>", SemanticSEO::render());
    }

    public function test_multiple_types()
    {
        SemanticSEO::is(Thing::class);
        SemanticSEO::is(WebSite::class);

        $this->assertStringContainsString(
            '<script type="application/ld+json">'.
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
                ]).
            '</script>',
            SemanticSEO::render()
        );
    }
}
