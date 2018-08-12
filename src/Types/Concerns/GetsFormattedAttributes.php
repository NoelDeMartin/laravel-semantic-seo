<?php

namespace NoelDeMartin\SemanticSEO\Types\Concerns;

use NoelDeMartin\SemanticSEO\Types\Thing;
use NoelDeMartin\SemanticSEO\Types\ImageObject;
use NoelDeMartin\SemanticSEO\Types\Contracts\HasTwitterHandle;

trait GetsFormattedAttributes
{
    public function getImageUrlFromAttribute($name)
    {
        $value = $this->getAttribute($name);

        if ($this->isType('url', $value)) {
            return $value;
        } else if ($this->isType(ImageObject::class, $value)) {
            return $value->getAttribute('url');
        } else {
            return null;
        }
    }

    public function getImageDescriptionFromAttribute($name)
    {
        $value = $this->getAttribute($name);

        if ($this->isType(ImageObject::class, $value)) {
            return $value->getAttribute('description');
        } else {
            return null;
        }
    }

    public function getDateFromAttribute($name)
    {
        $value = $this->getAttribute($name);

        if ($this->isType('date', $value)) {
            return $value->toISO8601String();
        } else {
            return null;
        }
    }

    public function getNameFromAttribute($name)
    {
        $value = $this->getAttribute($name);

        if ($this->isType(Thing::class, $value)) {
            return $value->getAttribute('name');
        } else {
            return null;
        }
    }

    public function getWordsArrayFromAttribute($name, $delimiter = ',')
    {
        $value = $this->getAttribute($name);

        if ($this->isType('text', $value)) {
            return array_map('trim', explode($delimiter, $value));
        } else {
            return null;
        }
    }

    public function getTwitterHandleFromAttribute($name)
    {
        $value = $this->getAttribute($name);

        if (!is_null($value) && $value instanceof HasTwitterHandle) {
            return $value->getTwitterHandle();
        }

        return null;
    }
}
