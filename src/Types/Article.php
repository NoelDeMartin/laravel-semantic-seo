<?php

namespace NoelDeMartin\SemanticSEO\Types;

use NoelDeMartin\SemanticSEO\SemanticSEO;

class Article extends CreativeWork
{
    public function beforeRender(SemanticSEO $seo)
    {
        $seo->opengraph(
            [
                'type' => 'article',
            ],
            false
        );

        parent::beforeRender($seo);
    }

    protected function getAttributeDefinitions()
    {
        return array_merge(parent::getAttributeDefinitions(), [
            'wordCount' => 'integer',
        ]);
    }
}
