<?php

namespace NoelDeMartin\SemanticSEO\Support\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO hide()
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO is(string $type)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO meta(array|string $fields, bool $override = true)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO openGraph(array|string $fields, mixed $override = true, bool $withoutPrefix = false)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO rss(string $url, string $title = 'RSS Feed')
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO sitemap(string $url, string $title = 'Sitemap')
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO twitter(array|string $fields, bool $override = true)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO title(string $title)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO titlePrefix(string $titlePrefix)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO titleSuffix(string $titleSuffix)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO rssUrl(string $url)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO rssTitle(string $title)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO sitemapUrl(string $url)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO sitemapTitle(string $title)
 * @method static \NoelDeMartin\SemanticSEO\SemanticSEO canonical(string $url)
 *
 * @see \NoelDeMartin\SemanticSEO\SemanticSEO
 */
class SemanticSEO extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'semantic-seo';
    }
}
