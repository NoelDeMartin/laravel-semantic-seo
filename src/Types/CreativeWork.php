<?php

namespace NoelDeMartin\SemanticSEO\Types;

use NoelDeMartin\SemanticSEO\SemanticSEO;

class CreativeWork extends Thing
{
    public function beforeRender(SemanticSEO $seo)
    {
        $seo->meta(
            $this->fillAttributeValues([
                'headline' => 'title',
            ]),
            false
        );

        $seo->twitter([
                'site' => $this->getTwitterHandleFromAttribute('creator'),
            ],
            false
        );

        parent::beforeRender($seo);
    }

    protected function getAttributeDefinitions()
    {
        return array_merge(parent::getAttributeDefinitions(), [
            'about' => Thing::class,
            'creator' => ['organization', Person::class],
            'author' => ['organization', Person::class],
            'headline' => 'text',
            'discussionUrl' => 'url',
            'inLanguage' => ['language', 'text'],
            'dateCreated' => 'date',
            'datePublished' => 'date',
            'dateModified' => 'date',
        ]);
    }
}
