<?php

namespace NoelDeMartin\SemanticSEO\Types;

use NoelDeMartin\SemanticSEO\Types\Concerns\HasTwitterHandle as HasTwitterHandleTrait;
use NoelDeMartin\SemanticSEO\Types\Contracts\HasTwitterHandle;

class Organization extends Thing implements HasTwitterHandle
{
    use HasTwitterHandleTrait;

    protected function getAttributeDefinitions(): array
    {
        return array_merge(parent::getAttributeDefinitions(), [
            'logo' => [ImageObject::class, 'url'],
        ]);
    }
}
