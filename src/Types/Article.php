<?php

namespace NoelDeMartin\SemanticSEO\Types;

use NoelDeMartin\SemanticSEO\SemanticSEO;

class Article extends CreativeWork
{
    public function beforeRender(SemanticSEO $seo): void
    {
        $seo->opengraph(
            [
                'type' => 'article',
            ],
            false
        );

        $seo->opengraph(
            $this->withoutEmptyValues([
                'article:published_time' => $this->getDateFromAttribute('datePublished'),
                'article:modified_time' => $this->getDateFromAttribute('dateModified'),
                'article:expiration_time' => $this->getDateFromAttribute('expires'),
                'article:author' => $this->getNameFromAttribute('author'),
                'article:section' => $this->getAttribute('articleSection'),
                'article:tag' => $this->getWordsArrayFromAttribute('keywords'),
            ]),
            false,
            true
        );

        parent::beforeRender($seo);
    }

    protected function getAttributeDefinitions(): array
    {
        return array_merge(parent::getAttributeDefinitions(), [
            'wordCount' => 'integer',
            'articleSection' => 'text',
        ]);
    }
}
