<?php

namespace NoelDeMartin\SemanticSEO;

use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

use NoelDeMartin\SemanticSEO\Types\AboutPage;
use NoelDeMartin\SemanticSEO\Types\Article;
use NoelDeMartin\SemanticSEO\Types\Blog;
use NoelDeMartin\SemanticSEO\Types\CollectionPage;
use NoelDeMartin\SemanticSEO\Types\Person;
use NoelDeMartin\SemanticSEO\Types\WebSite;

class SemanticSEO
{
    /**
     * Meta fields that are handled specially and should not be auto-generated.
     */
    protected $reservedMeta = [
        'title', 'title_prefix', 'title_suffix',
        'rss_url', 'rss_title',
        'sitemap_url', 'sitemap_title',
        'canonical',
    ];

    /**
     * Type aliases.
     */
    protected $typeAliases = [
        'blog' => Blog::class,
        'person' => Person::class,
        'website' => WebSite::class,
        'article' => Article::class,
        'about' => AboutPage::class,
        'collection' => CollectionPage::class,
    ];

    /**
     * Meta field values.
     */
    protected $meta = [];

    /**
     * Types.
     */
    protected $types = [];

    public function render()
    {
        foreach ($this->types as $type) {
            $type->beforeRender($this);
        }

        $html = '';
        $meta = $this->meta;

        if (array_key_exists('title', $meta) && !is_null($meta['title'])) {
            $titlePrefix = array_key_exists('title_prefix', $meta) ? $meta['title_prefix'] : '';
            $titleSuffix = array_key_exists('title_suffix', $meta) ? $meta['title_suffix'] : '';
            $html .= '<title>' . $titlePrefix . $meta['title'] . $titleSuffix . '</title>';
        }

        if (array_key_exists('rss_url', $meta) && is_string($meta['rss_url'])) {
            $html .= '<link rel="alternate" type="application/rss+xml" ' .
                ((array_key_exists('rss_title', $meta) && is_string($meta['rss_title']))
                    ? ('title="' . $meta['rss_title'] . '" ')
                    : '') .
                'href="' . $meta['rss_url'] . '">';
        }

        if (array_key_exists('sitemap_url', $meta) && is_string($meta['sitemap_url'])) {
            $html .= '<link rel="sitemap" type="application/xml" ' .
                ((array_key_exists('sitemap_title', $meta) && is_string($meta['sitemap_title']))
                    ? ('title="' . $meta['sitemap_title'] . '" ')
                    : '') .
                'href="' . $meta['sitemap_url'] . '">';
        }

        if (array_key_exists('canonical', $meta)) {
            if (is_string($meta['canonical'])) {
                $html .= '<link rel="canonical" href="' . $meta['canonical'] . '">';
            }
        } else {
            $html .= '<link rel="canonical" href="' . URL::current() . '">';
        }

        foreach ($this->reservedMeta as $field) {
            unset($meta[$field]);
        }

        foreach ($meta as $field => $value) {
            if (is_string($value)) {
                $html .= "<meta name=\"$field\" content=\"$value\">";
            } else if (is_array($value)) {
                $name = array_key_exists('name', $value) ? $value['name'] : $field;
                $property = array_key_exists('property', $value) ? $value['property'] : false;
                $contents = $value['content'];

                if (!is_array($contents)) {
                    $contents = [$contents];
                }
                foreach ($contents as $content) {
                    $html .= '<meta ';

                    if ($name) {
                        $html .= "name=\"$name\" ";
                    }

                    if ($property) {
                        $html .= "property=\"$property\" ";
                    }

                    $html .= "content=\"$content\">";
                }
            }
        }

        $ldJson = '';
        if (count($this->types) === 1) {
            $ldJson = json_encode(
                array_merge(
                    ['@context' => 'http://schema.org'],
                    $this->types[0]->toArray()
                )
            );
        } else if (count($this->types) > 0) {
            $ldJson = [
                '@context' => 'http://schema.org',
                '@graph' => [],
            ];

            foreach ($this->types as $type) {
                $ldJson['@graph'][] = $type->toArray();
            }

            $ldJson = json_encode($ldJson);
        }

        if (!empty($ldJson)) {
            $html .= "<script type=\"application/ld+json\">$ldJson</script>";
        }

        return $html;
    }

    public function hide()
    {
        return $this->meta('robots', 'noindex, nofollow');
    }

    public function rss($url, $title = 'RSS Feed')
    {
        $this->meta('rss_url', $url);
        $this->meta('rss_title', $title);

        return $this;
    }

    public function sitemap($url, $title = 'Sitemap')
    {
        $this->meta('sitemap_url', $url);
        $this->meta('sitemap_title', $title);

        return $this;
    }

    public function twitter($fields, $override = true)
    {
        if (!is_array($fields)) {
            $fields = [$fields => $override];
            $override = func_num_args() > 2 ? func_get_arg(2) : true;
        }

        foreach ($fields as $field => $value) {
            $this->meta('twitter:' . $field, $value, $override);
        }

        return $this;
    }

    public function openGraph($fields, $override = true, $withoutPrefix = false)
    {
        if (!is_array($fields)) {
            $fields = [$fields => $override];
            $override = func_num_args() > 2 ? func_get_arg(2) : true;
        }

        foreach ($fields as $field => $value) {
            $property = ($withoutPrefix ? '' : 'og:') . $field;
            $this->meta(
                $property,
                [
                    'name' => false,
                    'property' => $property,
                    'content' => $value,
                ],
                $override
            );
        }

        return $this;
    }

    public function meta($fields, $override = true)
    {
        if (!is_array($fields)) {
            $fields = [$fields => $override];
            $override = func_num_args() > 2 ? func_get_arg(2) : true;
        }

        foreach ($fields as $field => $value) {
            if ($override || !array_key_exists($field, $this->meta)) {
                $this->meta[$field] = $value;
            }
        }

        return $this;
    }

    public function is($type)
    {
        if (is_string($type)) {
            $type = App::make($type);
        }

        $this->types[] = $type;

        return $type;
    }

    public function __call($method, $parameters)
    {
        if (array_key_exists($method, $this->typeAliases)) {
            return $this->is(App::make($this->typeAliases[$method]));
        } else {
            return $this->meta(Str::snake($method), ...$parameters);
        }
    }
}
