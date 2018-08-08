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
}
