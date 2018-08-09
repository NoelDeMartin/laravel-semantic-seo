<?php

namespace NoelDeMartin\SemanticSEO;

use InvalidArgumentException;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\App;
use NoelDeMartin\SemanticSEO\Types\WebSite;

class SemanticSEO
{
    /**
     * Meta fields that are handled specially and should not be auto-generated.
     */
    protected $reservedMeta = [
        'title', 'title_prefix', 'title_suffix',
        'canonical',
    ];

    /**
     * Type aliases.
     */
    protected $typeAliases = [
        'website' => WebSite::class,
    ];

    /**
     * Meta field values.
     */
    protected $meta = [];

    /**
     * Types.
     */
    protected $types = [];

    public function render()
    {
        foreach ($this->types as $type) {
            $type->beforeRender($this);
        }

        $html = '';
        $meta = $this->meta;

        if (isset($meta['title'])) {
            $titlePrefix = isset($meta['title_prefix']) ? $meta['title_prefix'] : '';
            $titleSuffix = isset($meta['title_suffix']) ? $meta['title_suffix'] : '';
            $html .= '<title>' . $titlePrefix . $meta['title'] . $titleSuffix . '</title>';
        }

        if (isset($meta['canonical'])) {
            if (is_string($meta['canonical'])) {
                $html .= '<link rel="canonical" href="' . $meta['canonical'] . '" />';
            }
        } else {
            $html .= '<link rel="canonical" href="' . URL::current() . '" />';
        }

        foreach ($this->reservedMeta as $field) {
            unset($meta[$field]);
        }

        foreach ($meta as $field => $value) {
            if (is_string($value)) {
                $html .= "<meta name=\"$field\" content=\"$value\" />";
            } else {
                $name = isset($value['name']) ? $value['name'] : $field;
                $property = isset($value['property']) ? $value['property'] : false;
                $html .= '<meta ';
                if ($name) {
                    $html .= "name=\"$name\" ";
                }

                if ($property) {
                    $html .= "property=\"$property\" ";
                }

                $content = $value['content'];
                $html .= "content=\"$content\" />";
            }
        }

        foreach ($this->types as $type) {
            $html .= $type->render();
        }

        return $html;
    }

    public function hide()
    {
        return $this->meta('robots', 'noindex, nofollow');
    }

    public function twitter($fields, $override = true)
    {
        if (!is_array($fields)) {
            $fields = [$fields => $override];
            $override = func_num_args() > 2 ? func_get_arg(2) : true;
        }

        foreach ($fields as $field => $value) {
            $this->meta('twitter:' . $field, $value, $override);
        }

        return $this;
    }

    public function openGraph($fields, $override = true)
    {
        if (!is_array($fields)) {
            $fields = [$fields => $override];
            $override = func_num_args() > 2 ? func_get_arg(2) : true;
        }

        foreach ($fields as $field => $value) {
            $this->meta(
                'og:' . $field,
                [
                    'name' => false,
                    'property' => 'og:' . $field,
                    'content' => $value,
                ],
                $override
            );
        }

        return $this;
    }

    public function meta($fields, $override = true)
    {
        if (!is_array($fields)) {
            $fields = [$fields => $override];
            $override = func_num_args() > 2 ? func_get_arg(2) : true;
        }

        foreach ($fields as $field => $value) {
            if ($override || !isset($this->meta[$field])) {
                $this->meta[$field] = $value;
            }
        }

        return $this;
    }

    public function is($type)
    {
        if (is_string($type)) {
            $type = App::make($type);
        }

        $this->types[] = $type;

        return $type;
    }

    public function __call($method, $parameters)
    {
        if (isset($this->typeAliases[$method])) {
            return $this->is(App::make($this->typeAliases[$method]));
        } else {
            return $this->meta(snake_case($method), ...$parameters);
        }
    }
}
