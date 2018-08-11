<?php

namespace NoelDeMartin\SemanticSEO\Types;

use DateTime;
use BadMethodCallException;
use Illuminate\Support\Str;
use Illuminate\Support\Carbon;
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
            $this->withoutEmptyValues([
                'title' => $this->getAttribute('name'),
                'description' => $this->getAttribute('description'),
                'canonical' => $this->getAttribute('url'),
            ]),
            false
        );

        $seo->opengraph(
            $this->withoutEmptyValues([
                'type' => 'website',
                'title' => $this->getAttribute('name'),
                'description' => $this->getAttribute('description'),
                'image' => $this->getImageUrlFromAttribute('image'),
                'image:alt' => $this->getImageDescriptionFromAttribute('image'),
                'url' => $this->getAttribute('url'),
            ]),
            false
        );

        $seo->twitter(
            $this->withoutEmptyValues([
                'card' => 'summary',
                'title' => $this->getAttribute('name'),
                'description' => $this->getAttribute('description'),
                'image' => $this->getImageUrlFromAttribute('image'),
                'image:alt' => $this->getImageDescriptionFromAttribute('image'),
                'url' => $this->getAttribute('url'),
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
                } else if ($value instanceof Carbon) {
                    return $value->format(DateTime::ISO8601);
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

    protected function withoutEmptyValues($values)
    {
        $cleanValues = [];

        foreach ($values as $key => $value) {
            if (!is_null($value)) {
                $cleanValues[$key] = $value;
            }
        }

        return $cleanValues;
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
                return is_string($value);
            case 'url':
                return is_string($value) && Str::startsWith($value, 'http');
            case 'image':
            case 'integer':
                return is_int($value);
            case 'date':
                return $value instanceof Carbon;
            case Thing::class:
                return $value instanceof Thing;
            case ImageObject::class:
                return $value instanceof ImageObject;
            case Person::class:
                return $value instanceof Person;
            case Organization::class:
                return $value instanceof Organization;
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
                    $castedValue = (string) $value;
                    break;
                case 'integer':
                    $castedValue = (int) $value;
                    break;
                case 'date':
                    $castedValue = $value instanceof DateTime
                        ? Carbon::instance($value)
                        : Carbon::parse($value);
                    break;
                case Thing::class:
                case ImageObject::class:
                case Person::class:
                case Organization::class:
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
            'image' => [ImageObject::class, 'url'],
            'url' => 'url',
            'sameAs' => 'url',
            'mainEntityOfPage' => [CreativeWork::class, 'url'],
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
