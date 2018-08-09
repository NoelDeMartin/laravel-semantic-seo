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

        parent::beforeRender($seo);
    }

    protected function getAttributeDefinitions()
    {
        return array_merge(parent::getAttributeDefinitions(), [
            'about' => Thing::class,
            'headline' => 'text',
        ]);
    }
}
