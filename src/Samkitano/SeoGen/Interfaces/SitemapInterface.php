<?php namespace Samkitano\SeoGen\Interfaces;


interface SitemapInterface {


	/**
	 * Add a link to the sitemap
	 *
	 * @param $updated_at
	 * @param $route
	 *
	 * @return mixed
	 */
	public function addLink($updated_at, $route);

    /**
     * Add a collection of links to the sitemap
     *
     * @param object $collection
     * @param string $url_prefix
     * @return void
     */
    public function addCollection($collection, $url_prefix = null);

    /**
     * Get the content for the sitemap.xml file
     *
     * @return string
     */
    public function getSitemapXml();

}
