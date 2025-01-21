<?php

namespace Testing\Stubs;

use NoelDeMartin\SemanticSEO\Types\Person;

class JohnDoe extends Person
{
    public function __construct()
    {
        parent::__construct();

        $this->name('John Doe');
        $this->sameAs([
            'https://twitter.com/JohnDoe',
            'https://github.com/JohnDoe',
        ]);
    }

    protected function getType(): string
    {
        return 'Person';
    }
}
