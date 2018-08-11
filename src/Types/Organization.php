<?php

namespace NoelDeMartin\SemanticSEO\Types;

use NoelDeMartin\SemanticSEO\Types\Contracts\HasTwitterHandle;
use NoelDeMartin\SemanticSEO\Types\Concerns\HasTwitterHandle as HasTwitterHandleTrait;

class Organization extends Thing implements HasTwitterHandle
{
    use HasTwitterHandleTrait;

    protected function getAttributeDefinitions()
    {
        return array_merge(parent::getAttributeDefinitions(), [
            'logo' => [ImageObject::class, 'url'],
        ]);
    }
}
