<?php

namespace NoelDeMartin\SemanticSEO;

class SemanticSEO
{
    protected $title;

    protected $description;

    public function render()
    {
        $html = '';

        if (!is_null($this->title)) {
            $html .= '<title>' . $this->title . '</title>';
        }

        if (!is_null($this->description)) {
            $html .= '<meta name="description" content="' . $this->description . '" />';
        }

        return $html;
    }

    public function title($title)
    {
        $this->title = $title;

        return $this;
    }

    public function description($description)
    {
        $this->description = $description;

        return $this;
    }
}
