<?php

namespace NoelDeMartin\SemanticSEO;

use InvalidArgumentException;

class SemanticSEO
{
    /**
     * Meta fields that are handled specially and should not be auto-generated.
     */
    protected $reservedMeta = ['title', 'title_prefix', 'title_suffix'];

    /**
     * Meta field values.
     */
    protected $meta = [];

    public function render()
    {
        $html = '';
        $meta = $this->meta;

        if (isset($meta['title'])) {
            $titlePrefix = isset($meta['title_prefix']) ? $meta['title_prefix'] : '';
            $titleSuffix = isset($meta['title_suffix']) ? $meta['title_suffix'] : '';
            $html .= '<title>' . $titlePrefix . $meta['title'] . $titleSuffix . '</title>';
        }

        foreach ($this->reservedMeta as $field) {
            unset($meta[$field]);
        }

        foreach ($meta as $field => $value) {
            if (is_string($value)) {
                $html .= "<meta name=\"$field\" content=\"$value\" />";
            } else {
                $nameAttribute = isset($value['nameAttribute'])
                    ? $value['nameAttribute']
                    : 'name';
                $content = $value['content'];
                $html .= "<meta $nameAttribute=\"$field\" content=\"$content\" />";
            }
        }

        return $html;
    }

    public function title(string $title)
    {
        return $this->meta('title', $title);
    }

    public function titlePrefix($prefix)
    {
        return $this->meta('title_prefix', $prefix);
    }

    public function titleSuffix($suffix)
    {
        return $this->meta('title_suffix', $suffix);
    }

    public function description(string $description)
    {
        return $this->meta('description', $description);
    }

    public function hide()
    {
        return $this->meta('robots', 'noindex, nofollow');
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
}
