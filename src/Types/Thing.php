<?php

namespace NoelDeMartin\SemanticSEO\Types;

use BadMethodCallException;
use NoelDeMartin\SemanticSEO\SemanticSEO;

class Thing
{
    protected $attributes = [];

    protected $attributeTypes = [
        'name' => 'text',
        'description' => 'text',
    ];

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
                'title' => 'name',
                'description' => 'description',
            ]),
            false
        );
    }

    public function render()
    {
        return '<script type="application/ld+json">' .
            json_encode($this->toArray()) .
        '</script>';
    }

    protected function toArray()
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
            '@context' => 'http://schema.org',
            '@type' => 'Thing',
        ]);
    }

    protected function fillAttributeValues($attributes)
    {
        $values = [];

        foreach ($attributes as $key => $attribute) {
            if (isset($this->attributes[$attribute])) {
                $values[$key] = $this->attributes[$attribute];
            }
        }

        return $values;
    }

    public function __call($method, $parameters)
    {
        if (isset($this->attributeTypes[$method])) {
            $this->setAttribute($method, $parameters[0]);

            return $this;
        } else {
            $className = static::class;

            throw new BadMethodCallException("Call to undefined method {$className}::{$method}()");
        }
    }
}
