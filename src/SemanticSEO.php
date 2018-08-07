<?php

namespace NoelDeMartin\SemanticSEO;

class SemanticSEO
{
    protected $title;

    public function render()
    {
        $html = '';

        if (!is_null($this->title)) {
            $html .= '<title>' . $this->title . '</title>';
        }

        return $html;
    }

    public function title($title)
    {
        $this->title = $title;

        return $this;
    }
}
