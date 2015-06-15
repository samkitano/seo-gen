<?php namespace Samkitano\SeoGen;

use \App;
use \Config;
use \Samkitano\SeoGen\Robots;
use \Samkitano\SeoGen\Sitemap;
use \Samkitano\SeoGen\Support\Helpers;
use \Illuminate\Support\ServiceProvider;

class SeoGenServiceProvider extends ServiceProvider {

	/**
	 * Indicates if loading of the provider is deferred.
	 *
	 * @var bool
	 */
	protected $defer = false;

	private $protocol;
	private $host;
	private $field_names;
	private $lang_file;
	private $languages;
	private $default_lang;

	/**
	 * Register the service provider.
	 *
	 * @return void
	 */
	public function register()
	{
		App::bind('SeoGen.sitemap', function()
		{
			return new Sitemap(new Helpers,
			                   $this->protocol,
			                   $this->host,
			                   $this->field_names,
			                   $this->lang_file,
			                   $this->languages,
			                   $this->default_lang);
		});

		App::bind('SeoGen.robots', function()
		{
			return new Robots(new Helpers,
			                  $this->protocol,
			                  $this->host,
			                  $this->field_names,
			                  $this->lang_file,
			                  $this->languages,
			                  $this->default_lang);
		});
	}

	/**
	 * Bootstrap the application events.
	 *
	 * @return void
	 */
	public function boot()
	{
		$this->package('samkitano/seo-gen', null, __DIR__ . '/../../../src');

		$this->protocol     = Config::get('seo-gen::protocol');
		$this->host         = Config::get('seo-gen::host');
		$this->field_names  = Config::get('seo-gen::fields');
		$this->lang_file    = Config::get('seo-gen::translated_routes_file');
		$this->languages    = Config::get('app.languages');
		$this->default_lang = Config::get('app.locale');
	}

	/**
	 * Get the services provided by the provider.
	 *
	 * @return array
	 */
	public function provides()
	{
		return array(
			'seo-gen.sitemap',
			'seo-gen.robots'
		);
	}

}
