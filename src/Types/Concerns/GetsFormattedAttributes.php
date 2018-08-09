<?php

namespace NoelDeMartin\SemanticSEO\Types\Concerns;

use NoelDeMartin\SemanticSEO\Types\Contracts\HasTwitterHandle;

trait GetsFormattedAttributes
{
    public function getTwitterHandleFromAttribute($name)
    {
        $value = $this->getAttribute($name);

        if (!is_null($value) && $value instanceof HasTwitterHandle) {
            return $value->getTwitterHandle();
        }

        return null;
    }
}
