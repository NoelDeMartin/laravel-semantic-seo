<?php

namespace NoelDeMartin\SemanticSEO;

use InvalidArgumentException;

class SemanticSEO
{
    /**
     * Meta field values.
     */
    protected $meta = [];

    public function render()
    {
        $html = '';
        $meta = $this->meta;

        if (isset($meta['title'])) {
            $html .= '<title>' . $meta['title'] . '</title>';
            unset($meta['title']);
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
        $this->meta['title'] =  $title;

        return $this;
    }

    public function description(string $description)
    {
        $this->meta['description'] =  $description;

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
    }
}
