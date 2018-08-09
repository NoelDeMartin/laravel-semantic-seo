<?php

namespace NoelDeMartin\SemanticSEO\Types;

use BadMethodCallException;
use NoelDeMartin\SemanticSEO\SemanticSEO;

class Thing
{
    protected $attributes = [];

    protected $attributeDefinitions = [];

    public function __construct()
    {
        $this->attributeDefinitions = $this->getAttributeDefinitions();
    }

    public function setAttribute($attribute, $value)
    {
        $this->attributes[$attribute] = $value;

        return $this;
    }

    public function setAttributes($attributes)
    {
        foreach ($attributes as $attribute => $value) {
            $this->setAttribute($attribute, $value);
        }

        return $this;
    }

    public function beforeRender(SemanticSEO $seo)
    {
        $seo->meta(
            $this->fillAttributeValues([
                'name' => 'title',
                'description' => 'description',
                'url' => 'canonical',
            ]),
            false
        );
    }

    public function toArray()
    {
        $attributes = array_map(
            function ($value) {
                if ($value instanceof Thing) {
                    return $value->toArray();
                } else {
                    return $value;

                }
            },
            $this->attributes
        );

        return array_merge($attributes, [
            '@type' => $this->getType(),
        ]);
    }

    protected function fillAttributeValues($attributes)
    {
        $values = [];

        foreach ($attributes as $attribute => $target) {
            if (array_key_exists($attribute, $this->attributes)) {
                $values[$target] = $this->attributes[$attribute];
            }
        }

        return $values;
    }

    protected function getAttributeDefinitions()
    {
        return [
            'name' => 'text',
            'description' => 'text',
            'image' => ['image', 'url'],
            'url' => 'url',
            'sameAs' => 'url',
        ];
    }

    protected function getType()
    {
        return class_basename($this);
    }

    public function __call($method, $parameters)
    {
        if (array_key_exists($method, $this->attributeDefinitions)) {
            $this->setAttribute($method, ...$parameters);

            return $this;
        } else {
            $className = static::class;

            throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
        }
    }
}
