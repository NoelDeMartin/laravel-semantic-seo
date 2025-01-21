<?php

namespace NoelDeMartin\SemanticSEO\Types\Concerns;

use NoelDeMartin\SemanticSEO\Types\Contracts\HasTwitterHandle;
use NoelDeMartin\SemanticSEO\Types\ImageObject;
use NoelDeMartin\SemanticSEO\Types\Thing;

trait GetsFormattedAttributes
{
    public function getImageUrlFromAttribute(string $name): ?string
    {
        $value = $this->getAttribute($name);

        if ($this->isType('url', $value)) {
            return $value;
        }

        if ($this->isType(ImageObject::class, $value)) {
            return $value->getAttribute('url');
        }

        return null;
    }

    public function getImageDescriptionFromAttribute(string $name): ?string
    {
        $value = $this->getAttribute($name);

        if ($this->isType(ImageObject::class, $value)) {
            return $value->getAttribute('description');
        }

        return null;
    }

    public function getDateFromAttribute(string $name): ?string
    {
        $value = $this->getAttribute($name);

        if ($this->isType('date', $value)) {
            return $value->toISO8601String();
        }

        return null;
    }

    public function getNameFromAttribute(string $name): ?string
    {
        $value = $this->getAttribute($name);

        if ($this->isType(Thing::class, $value)) {
            return $value->getAttribute('name');
        }

        return null;
    }

    public function getWordsArrayFromAttribute(string $name, string $delimiter = ','): ?array
    {
        $value = $this->getAttribute($name);

        if ($this->isType('text', $value)) {
            return array_map('trim', explode($delimiter, $value));
        }

        return null;
    }

    public function getTwitterHandleFromAttribute(string $name): ?string
    {
        $value = $this->getAttribute($name);

        if (! is_null($value) && $value instanceof HasTwitterHandle) {
            return $value->getTwitterHandle();
        }

        return null;
    }
}
