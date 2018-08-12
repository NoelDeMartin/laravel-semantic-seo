# Laravel Semantic SEO [![Build Status](https://semaphoreci.com/api/v1/noeldemartin/laravel-semantic-seo/branches/master/badge.svg)](https://semaphoreci.com/noeldemartin/laravel-semantic-seo)

Use this package to define Semantic SEO information through meta tags and structure data types.

## Installation

Install using composer:

```
composer require --dev noeldemartin/laravel-semantic-seo
```

The package will be automatically loaded using Laravel's [package discovery](https://laravel.com/docs/5.6/packages#package-discovery).

## Usage

This package helps to scaffold [Semantic HTML](https://en.wikipedia.org/wiki/Semantic_HTML) for a website defining two things:

- **Meta tags:** The most common approach to define semantic information in a website is to include meta tags with specific information of the current page. This is how most social networks create a preview when sharing links, for example [Twitter Cards](https://developer.twitter.com/en/docs/tweets/optimize-with-cards/guides/getting-started) or [Facebook Open Graph](http://ogp.me/). Search engines also use some of these tags to format how a link appears on the search results.

- **Types:** In order to provide richer semantics, types can be defined for specific use cases such as Blog Posts, Persons, Web Applications, etc. The type system used in this package provides [schema.org](https://schema.org) schemas that are a common way of defined [Structured Data](https://developers.google.com/search/docs/guides/intro-structured-data). One advantage of defining this richer data, is that meta tags can be inferred from this types and information doesn't need to be repeated.

The markup generated with this package should be placed on the `head` element. A Blade directive named `semanticSEO` is included with this package:

```blade.php
<html>
    <head>
        @semanticSEO

        // Other tags in head such as css or js assets
    </head>
    <body>
        // Page content
    </body>
</html>
```

In order to define Types and Meta tags, use the `SemanticSEO` facade.

### Using Types

Not all types are available with this package, but most common are. For example, you could define the information for a [WebSite](https://schema.org/WebSite) schema in your page as such:

```php
SemanticSEO::website()
    ->url(route('home'))
    ->name('My Website');
```

You can place this code wherever you want, for example on the Controller handler methods.

Types automatically add meta tags that make sense, for example the code above would generate the following markup:

```html
<title>My Website</title>
<link rel="canonical" href="https://mywebsite.com">
<meta property="og:type" content="website">
<meta property="og:title" content="My Website">
<meta property="og:url" content="https://mywebsite.com">
<meta name="twitter:card" content="summary">
<meta name="twitter:title" content="My Website">
<meta name="twitter:url" content="https://mywebsite.com">
<script type="application/ld+json">
    {
        "@context":"http:\/\/schema.org",
        "url":"https:\/\/mywebsite.com",
        "name":"My Website",
        "@type":"WebSite"
    }
</script>
```

Other than the available types, any new types can be used with the method `is` like so:

```php
SemanticSEO::is(MyType::class)
    ->myAttribute('foobar');
```

To see more examples of what's available and how to use existing types, take a look to the tests in `TypesTests.php` file under /tests.

### Using Meta tags

Some times it is necessary to define meta tags explicitly. It's also possible that for some reason you don't want to use types, in which case meta tags can be defined one by one:

```php
SemanticSEO::meta('title', 'My Website');
SemanticSEO::meta('description', 'My Website is awesome');
```

Keep in mind that this package is aware of some special meta tags, for example using the "title" meta tag will actually render as a `<title>` tag and not `<meta>`. Multiple meta tags can also be defined at once (for example, if you keep the content of this tags in translation files):

```php
SemanticSEO::meta([
    'title' => trans('seo.title'),
    'description' => trans('seo.description'),
]);
```

To see more examples of what's available and how to use existing meta tags, take a look to the tests in `MetaTagsTests.php` file under /tests.

Some other special meta tags are the following:

#### Robots

To hide one page from crawlers, call `SemanticSEO::hide()` which will generate:

```html
<meta name="robots" content="noindex, nofollow">
```

A middleware has also been registered in case this wants to be called from the routes file (for example, when using the `view` short-hand to define routes):

```php
Route::view('secret', 'secret')->middleware('semantic-seo:hide');
```

#### Sitemap and RSS

To define sitemap or rss xml locations (this could be placed in a Service Provider to prevent calling this in every controller):

```php
SemanticSEO::rss(url('blog/rss.xml'), 'My RSS Feed');
SemanticSEO::sitemap(url('sitemap.xml'), 'My Sitemap');
```

Which will generate:

```html
<link rel="alternate" type="application/rss+xml" title="My RSS Feed" href="https://mywebsite.com/blog/rss.xml">"
<link rel="sitemap" type="application/xml" title="My Sitemap" href="https://mywebsite.com/sitemap.xml">"
```

#### Canonical url

The canonical location of the page is automatically generated for every page, this can be disabled setting it as `false` (as well as any meta overriding by Types):

```php
SemanticSEO::canonical(false);
```

#### Social networks

Some short-hands are also defined for twitter and open graph, which will automatically include `twitter:` and `og:` prefixes:

```php
SemanticSEO::twitter('title', 'Twitter title');
SemanticSEO::openGraph('title', 'Open Graph title');
```

Which will generate:

```html
<meta name="twitter:title" content="Twitter title">
<meta property="og:title" content="Open Graph title">
```

