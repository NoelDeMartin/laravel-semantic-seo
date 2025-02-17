<?php

namespace NoelDeMartin\SemanticSEO\Types;

use BadMethodCallException;
use DateTime;
use Exception;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Str;
use NoelDeMartin\SemanticSEO\SemanticSEO;
use NoelDeMartin\SemanticSEO\Types\Concerns\GetsFormattedAttributes;

class Thing
{
    use GetsFormattedAttributes;

    protected array $attributes = [];

    protected array $attributeDefinitions = [];

    public function __construct()
    {
        $this->attributeDefinitions = $this->getAttributeDefinitions();
    }

    public function hasAttribute(string $name): bool
    {
        return array_key_exists($name, $this->attributes);
    }

    public function getAttribute(string $name): mixed
    {
        return $this->hasAttribute($name) ? $this->attributes[$name] : null;
    }

    public function setAttribute(string $name, mixed $value): self
    {
        $this->attributes[$name] = $this->castAttribute($name, $value);

        return $this;
    }

    public function setAttributes(array $attributes): self
    {
        foreach ($attributes as $name => $value) {
            $this->setAttribute($name, $value);
        }

        return $this;
    }

    public function beforeRender(SemanticSEO $seo): void
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

    public function toArray(): array
    {
        $attributes = $this->withoutEmptyValues(
            $this->convertValues($this->attributes)
        );

        return array_merge($attributes, [
            '@type' => $this->getType(),
        ]);
    }

    protected function convertValues(array $attributes): array
    {
        return array_map(
            function ($value) {
                if ($value instanceof Thing) {
                    return $value->toArray();
                } elseif ($value instanceof Carbon) {
                    return $value->toISO8601String();
                } elseif (is_array($value)) {
                    return $this->convertValues($value);
                } else {
                    return $value;
                }
            },
            $attributes
        );
    }

    protected function withoutEmptyValues(array $values): array
    {
        $cleanValues = [];

        foreach ($values as $key => $value) {
            if (! is_null($value)) {
                $cleanValues[$key] = $value;
            }
        }

        return $cleanValues;
    }

    protected function castAttribute(string $name, mixed $values): mixed
    {
        $types = $this->attributeDefinitions[$name];
        if (! is_array($types)) {
            $types = [$types];
        }

        $originalArray = is_array($values);
        if (! $originalArray) {
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

            if (! $typesMatch[$i]) {
                foreach ($types as $type) {
                    $castedValue = $this->castValue($type, $value);

                    if (! is_null($castedValue)) {
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

    protected function isType(string $type, mixed $value): bool
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
            case CreativeWork::class:
                return $value instanceof CreativeWork;
            default:
                return false;
        }
    }

    protected function castValue(string $type, mixed $value): mixed
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
                case CreativeWork::class:
                    $castedValue = App::make($value);
                    break;
            }

            return $this->isType($type, $castedValue) ? $castedValue : null;
        } catch (Exception $e) {
            return null;
        }
    }

    protected function getAttributeDefinitions(): array
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

    protected function getType(): string
    {
        return class_basename($this);
    }

    public function __call(string $method, array $parameters): self
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
