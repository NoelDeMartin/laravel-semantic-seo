<?php

namespace NoelDeMartin\SemanticSEO\Types;

use NoelDeMartin\SemanticSEO\SemanticSEO;

class CreativeWork extends Thing
{
    public function beforeRender(SemanticSEO $seo)
    {
        $seo->meta(
            $this->withoutEmptyValues([
                'title' => $this->getAttribute('headline'),
            ]),
            false
        );

        $seo->twitter(
            $this->withoutEmptyValues([
                'site' => $this->getTwitterHandleFromAttribute('creator'),
            ]),
            false
        );

        parent::beforeRender($seo);
    }

    protected function getAttributeDefinitions()
    {
        return array_merge(parent::getAttributeDefinitions(), [
            'about' => Thing::class,
            'creator' => [Organization::class, Person::class],
            'author' => [Organization::class, Person::class],
            'publisher' => [Organization::class, Person::class],
            'headline' => 'text',
            'discussionUrl' => 'url',
            'inLanguage' => ['language', 'text'],
            'hasPart' => [CreativeWork::class, 'trip'],
            'dateCreated' => 'date',
            'datePublished' => 'date',
            'dateModified' => 'date',
            'expires' => 'date',
            'keywords' => 'text',
        ]);
    }
}
