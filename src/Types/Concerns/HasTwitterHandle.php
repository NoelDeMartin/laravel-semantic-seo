<?php

namespace NoelDeMartin\SemanticSEO\Types\Concerns;

trait HasTwitterHandle
{
    public function getTwitterHandle()
    {
        if ($this->hasAttribute('sameAs')) {
            $sameAs = $this->getAttribute('sameAs');

            if (!is_array($sameAs)) {
                $sameAs = [$sameAs];
            }

            foreach ($sameAs as $url) {
                if (str_is('https://twitter.com/*', $url)) {
                    return '@' . substr($url, strlen('https://twitter.com/'));
                }
            }
        }

        return null;
    }
}
