## SEO Aggregator

Generate robots.txt and multi-language sitemap.xml files in Laravel 4.2.

Based on [Hettiger/seo-aggregator](https://github.com/hettiger/seo-aggregator)

### Installation

Require with composer

```js
// composer.json

"require": {
    "php": ">=5.4.0",
    "samkitano/seo-gen": "dev-master",
    // ...
},
```

Add provider in `app\config\app.php`

```php
// ...
'Samkitano\SeoGen\SeoGenServiceProvider',
```

Publish configuration file

```bash
php artisan config:publish samkitano/seo-gen
```

Modify `app\config\packages\samkitano\seo-gen\config.php` to suit your needs.

Provide the name of the file you are using to translate your routes in `'translated_routes_file'`. Do not include extension.

If you don't need to translate your url prefix (you wouldn't be here in that case now, would you?) you should probably use another package, but feel free to open a pull request and make some changes :)

For example purposes we will use the default `'translated_routes_file' => 'routes',`

### Usage examples with Laravel 4.2

Obviously, you will need to have your translation files ready:

```php
/**
* app\lang\en\routes.php
*/

return array(
	'home'      => 'home',
	'portfolio' => 'portfolio',
	'contacts'  => 'contacts',
	'pages'     => 'pages',
	// ...
);
```

```php
/**
* app\lang\pt\routes.php
*/

return array(
	'home'      => 'inicio',
	'portfolio' => 'portefolio',
	'contacts'  => 'contactos',
	'pages'     => 'paginas',
	// ...
);
```

```php
/**
* app\lang\fr\routes.php
*/

return array(
	'home'      => 'accueil',
	'portfolio' => 'portefeuille',
	'contacts'  => 'contacts',
	'pages'     => 'pages',
	// ...
);
```

```php
/**
* app\lang\de\routes.php
*/

return array(
	'home'      => 'startseite',
	'portfolio' => 'portefeuille',
	'contacts'  => 'kontakte',
	'pages'     => 'seiten',
	// ...
);
```

SeoGen will take a look at your app configuration file to find out your app's default language and the available languages.

```php
/**
* app\config\app.php
*/

	/*
	|--------------------------------------------------------------------------
	| Application Locale Configuration
	|--------------------------------------------------------------------------
	|
	| The application locale determines the default locale that will be used
	| by the translation service provider. You are free to set this value
	| to any of the locales which will be supported by the application.
	|
	*/

	'locale' => 'en',

	/*
	|--------------------------------------------------------------------------
	| Available Languages
	|--------------------------------------------------------------------------
	|
	| Supported Languages
	|
	*/

	'languages' => array('en', 'pt', 'fr', 'de'),

```

Now you can use SeoGen wherever you like in your app.

For the sake of this example's simplicity we will do it right in `app\routes.php`, but you should use a controller instead.

```php
/**
* app/routes.php
*/

use Samkitano\SeoGen\Facades\Sitemap;
use Samkitano\SeoGen\Facades\Robots;

// Language Selection
$languages  = array('en', 'pt', 'fr', 'de');
$locale     = Request::segment(1);

if ( in_array($locale, $languages) )
{
	\App::setLocale($locale);
	Session::put('locale', $locale);
}
else
{
	$locale = null;
}

Route::group( array('prefix' => $locale),
	function()
	{
		Route::get( trans('routes.home'),
			array('as' => 'home', 'uses' => 'ExampleController@home')
		);

		Route::get( trans('routes.portfolio'),
			array('as' => 'portfolio', 'uses' => 'ExampleController@portfolio')
		);

		Route::get( trans('routes.contacts'),
			array('as' => 'contacts', 'uses' => 'ExampleController@contacts')
		);

		Route::get( trans('routes.pages' . '/{slug}'),
			array('as' => 'pages', 'uses' => 'ExampleController@pages')
		);
	}
);

Route::get('sitemap.xml', function()
{
	$date_time = new DateTime('now');

	Sitemap::addLink($date_time, 'home');
	Sitemap::addLink($date_time, 'portfolio');
	Sitemap::addLink($date_time, 'contacts');

	$collection = Pages::all();
	Sitemap::addCollection($collection, 'pages');

	return Response::make( Sitemap::getSitemapXml() )
	               ->header('Content-Type', 'text/xml');
});

Route::get('robots.txt', function()
{
	Robots::disallowPath('/admin');
    return Response::make( Robots::getRobotsDirectives(true) )
    			   ->header('Content-Type', 'text/plain');
});

```

The above example should return a sitemap with something like

```xml
<?xml version="1.0" encoding="UTF-8"?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">
	<url>
		<loc>http://example.com/en/home</loc>
		<xhtml:link rel="alternate" hreflang="pt" href="http://example.com/pt/inicio"/>
		<xhtml:link rel="alternate" hreflang="fr" href="http://example.com/fr/accueil"/>
		<xhtml:link rel="alternate" hreflang="de" href="http://example.com/de/startseite"/>
		<lastmod>2015-06-16</lastmod>
	</url>
	<url>
		<loc>http://example.com/en/portfolio</loc>
		<xhtml:link rel="alternate" hreflang="pt" href="http://example.com/pt/portefolio"/>
		<xhtml:link rel="alternate" hreflang="fr" href="http://example.com/fr/portefeuille"/>
		<xhtml:link rel="alternate" hreflang="de" href="http://example.com/de/portefeuille"/>
		<lastmod>2015-06-16</lastmod>
	</url>
	<url>
		<loc>http://example.com/en/contacts</loc>
		<xhtml:link rel="alternate" hreflang="pt" href="http://example.com/pt/contactos"/>
		<xhtml:link rel="alternate" hreflang="fr" href="http://example.com/fr/contacts"/>
		<xhtml:link rel="alternate" hreflang="de" href="http://example.com/de/kontakte"/>
		<lastmod>2015-06-16</lastmod>
	</url>
	<url>
		<loc>http://example.com/en/pages/page-1</loc>
		<xhtml:link rel="alternate" hreflang="pt" href="http://example.com/pt/paginas/page-1"/>
		<xhtml:link rel="alternate" hreflang="fr" href="http://example.com/fr/pages/page-1"/>
		<xhtml:link rel="alternate" hreflang="de" href="http://example.com/de/seiten/page-1"/>
		<lastmod>2015-06-10</lastmod>
	</url>
	<url>
		<loc>http://example.com/en/pages/page-2</loc>
		<xhtml:link rel="alternate" hreflang="pt" href="http://example.com/pt/paginas/page-2"/>
		<xhtml:link rel="alternate" hreflang="fr" href="http://example.com/fr/pages/page-2"/>
		<xhtml:link rel="alternate" hreflang="de" href="http://example.com/de/seiten/page-2"/>
		<lastmod>2015-06-10</lastmod>
	</url>
	<url>
		<loc>http://example.com/en/pages/page-3</loc>
		<xhtml:link rel="alternate" hreflang="pt" href="http://example.com/pt/paginas/page-3"/>
		<xhtml:link rel="alternate" hreflang="fr" href="http://example.com/fr/pages/page-3"/>
		<xhtml:link rel="alternate" hreflang="de" href="http://example.com/de/seiten/page-3"/>
		<lastmod>2015-06-10</lastmod>
	<url>
</urlset>
```

and robots.txt

```txt
User-agent: *
Disallow: /admin

Sitemap: http://example.com/sitemap.xml
```

### Notes
SeoGen does NOT translate slugs for the time being.

### License

[SEO Aggregator](https://github.com/hettiger/seo-aggregator) is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)

[SEO GEN](https://github.com/samkitano/seo-gen) is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT)