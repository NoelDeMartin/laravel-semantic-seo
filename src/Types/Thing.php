<?php

namespace NoelDeMartin\SemanticSEO\Types;

use BadMethodCallException;
use Illuminate\Support\Facades\App;
use NoelDeMartin\SemanticSEO\SemanticSEO;
use NoelDeMartin\SemanticSEO\Types\Concerns\GetsFormattedAttributes;

class Thing
{
    use GetsFormattedAttributes;

    protected $attributes = [];

    protected $attributeDefinitions = [];

    public function __construct()
    {
        $this->attributeDefinitions = $this->getAttributeDefinitions();
    }

    public function hasAttribute($name)
    {
        return array_key_exists($name, $this->attributes);
    }

    public function getAttribute($name)
    {
        return $this->hasAttribute($name) ? $this->attributes[$name] : null;
    }

    public function setAttribute($name, $value)
    {
        $this->attributes[$name] = $this->castAttribute($name, $value);

        return $this;
    }

    public function setAttributes($attributes)
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
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

        $seo->opengraph(
            array_merge(
                ['type' => 'website'],
                $this->fillAttributeValues([
                    'name' => 'title',
                    'description' => 'description',
                    'image' => 'image',
                    'url' => 'url',
                ])
            ),
            false
        );

        $seo->twitter(
            array_merge(
                ['card' => 'summary'],
                $this->fillAttributeValues([
                    'image' => 'image',
                    'url' => 'url',
                ])
            ),
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
            if ($this->hasAttribute($attribute)) {
                $values[$target] = $this->getAttribute($attribute);
            }
        }

        return $values;
    }

    protected function castAttribute($name, $values)
    {
        $types = $this->attributeDefinitions[$name];
        if (!is_array($types)) {
            $types = [$types];
        }

        $originalArray = is_array($values);
        if (!$originalArray) {
            $values = [$values];
        }

        $typesMatch = array_fill(0, count($values), false);
        foreach ($values as $i => $value) {
            foreach ($types as $type) {
                if ($this->isType($type, $value)) {
                    $typesMatch[$i] = true;
                    break;
                }
            }

            if (!$typesMatch[$i]) {
                foreach ($types as $type) {
                    $castedValue = $this->castValue($type, $value);

                    if (!is_null($castedValue)) {
                        $values[$i] = $castedValue;
                        $typesMatch[$i] = true;
                        break;
                    }
                }
            }
        }

        if (array_unique($typesMatch) === [true]) {
            return $originalArray ? $values : $values[0];
        } else {
            return null;
        }
    }

    protected function isType($type, $value)
    {
        switch ($type) {
            case 'text':
            case 'url':
            case 'image':
                return is_string($value);
            case Thing::class:
                return $value instanceof Thing;
            case Person::class:
                return $value instanceof Person;
            default:
                return false;
        }
    }

    protected function castValue($type, $value)
    {
        try {
            $castedValue = null;
            switch ($type) {
                case 'text':
                case 'url':
                case 'image':
                    $castedValue = (string) $value;
                    break;
                case Thing::class:
                case Person::class:
                    $castedValue = App::make($value);
                    break;
            }

            return $this->isType($type, $castedValue) ? $castedValue : null;
        } catch (\Exception $e) {
            return null;
        }
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
