<?php namespace Samkitano\SeoGen;

use Illuminate\Support\Facades\Config;
use Samkitano\SeoGen\Interfaces\SitemapInterface;
use Samkitano\SeoGen\Support\CustomObject;
use Illuminate\Support\Facades\Lang;
use \App;

class Sitemap extends Generator implements SitemapInterface {

    protected $links;
    protected $collections;
    protected $lines;


	/**
	 * Add a link to the sitemap
	 *
	 * @param $updated_at
	 * @param $route
	 *
	 * @return mixed|void
	 */
	public function addLink($updated_at, $route)
    {
	    $link_obj = new CustomObject();

	    // set default language link
	    $link_obj->link
		    = $route == '/'
		    ? ''
		    : '/' . $this->default_lang . '/' . \Lang::get("{$this->lang_file}.{$route}");

	    // set link updated tag
	    $link_obj->updated_at = $updated_at;

		// Get current language
		$old_lang = App::getLocale();

	    // Iterate languages
	    foreach ( $this->languages as $language )
	    {
		    if ( $language != $this->default_lang ) // ignore default language
		    {
			    // Set App Lang to default
			    App::setLocale($language);

			    // translate route
			    $translated_route
				    = $route == '/'
				    ? ''
				    : \Lang::get("{$this->lang_file}.{$route}");

			    // get alternate link
			    $link_obj->alternate[$language] = $this->addAlternate($translated_route, $language);
		    }
	    }

		// restore App Lang
		App::setLocale($old_lang);

	    $this->links[] = $link_obj;
    }

    /**
     * Add a collection of links to the sitemap
     *
     * @param object $collection
     * @param string $url_prefix
     * @return void
     */
    public function addCollection($collection, $url_prefix = null)
    {
        foreach ( $collection as $link )
        {
            $link->prefix = $url_prefix;
        }

        $this->collections[] = $collection;
    }

	/**
	 * Add Alternate link line to the sitemap
	 *
	 * @param $link
	 * @param $lang
	 *
	 * @return string
	 */
	private function addAlternate($link, $lang)
	{
		return '<xhtml:link rel="alternate" hreflang="'
		       . $lang
		       . '" href="'
		       . rtrim( $this->helpers->url( $link, $this->protocol, $this->host, "$lang/" ), '/')
		       . '" />';
	}

    /**
     * Add one line to the output
     *
     * @param string $line
     * @return void
     */
    protected function addLine($line)
    {
        $this->lines[] = $line;
    }

    /**
     * Iterate through a collection and add lines accordingly
     *
     * @param object $collection
     */
    protected function iterateCollection($collection)
    {
        $loc     = $this->field_names['loc'];
        $lastmod = $this->field_names['lastmod'];

        foreach ( $collection as $link )
        {
            if ( ! is_null($link->prefix) )
            {
	            // translate route
                $prefix = \Lang::get("$this->lang_file.$link->prefix") . '/';
            }

	        // prepare default url
	        $l = $this->helpers->url( $prefix . $link->$loc, $this->protocol, $this->host, $this->default_lang . '/');

	        // get current locale
	        $old_lang = \App::getLocale();

	        // iterate languages
	        foreach ( $this->languages as $language )
	        {
		        if ($language != $this->default_lang) // ignore default language
		        {
			        App::setLocale($language);

					// prepare alternate link
			        $prefix = \Lang::get("$this->lang_file.$link->prefix") . '/';

			        // append link to alternates array
			        $a[ $language ] = $this->helpers->url( $prefix . $link->$loc, $this->protocol, $this->host, $language . '/' );
		        }
	        }

	        // restore app locale
	        App::setLocale($old_lang);

	        // open url tag
            $this->addLine('<url>');

	        // add default language tag
            $this->addLine('<loc>');
            $this->addLine($l);
            $this->addLine('</loc>');

	        // iterate alternates array
	        foreach ( $a as $lang=>$alt )
	        {
		        // append to url tag
		        $this->addLine('<xhtml:link rel="alternate" hreflang="' . $lang . '" href="' . $alt . '" />');
	        }

	        // append lastmod tag
            $this->addLine('<lastmod>' . date_format($link->$lastmod, 'Y-m-d') . '</lastmod>');

	        // close url tag
            $this->addLine('</url>');
        }

    }

    /**
     * Generate the content of all links for the sitemap
     *
     * @return void
     */
    protected function generateLinks()
    {
        if ( ! is_null($this->links) )
        {
            foreach ( $this->links as $link )
            {
                $this->addLine( '<url>' );
                $this->addLine( '<loc>' );
                $this->addLine( $this->helpers->url($link->link, $this->protocol, $this->host) );
                $this->addLine( '</loc>' );

	            foreach ( $this->languages as $language )
	            {
		            if ($language != $this->default_lang)
		            {
			            $this->addLine( $link->alternate[ $language ] );
		            }
	            }

	            $this->addLine('<lastmod>' . date_format($link->updated_at, 'Y-m-d') . '</lastmod>');
                $this->addLine('</url>');
            }
        }
    }

    /**
     * Generate the content of all collections for the sitemap
     *
     * @return void
     */
    protected function generateCollections()
    {
        if ( ! is_null($this->collections) )
        {
            foreach ( $this->collections as $collection )
            {
                $this->iterateCollection($collection);
            }
        }
    }

    /**
     * Get the content for the sitemap.xml file
     *
     * @return string
     */
    public function getSitemapXml()
    {
        $this->addLine('<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9" xmlns:xhtml="http://www.w3.org/1999/xhtml">');
        $this->generateLinks();
        $this->generateCollections();
        $this->addLine('</urlset>');

        $output = implode('', $this->lines);

        return $output;
    }

}
