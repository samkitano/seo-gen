<?php namespace Samkitano\SeoGen\Support;

use Samkitano\SeoGen\Interfaces\HelpersInterface;

class Helpers implements HelpersInterface {

    /**
     * Get the full URL to a given path
     * (e.g. //domain.tld/path)
     *
     * @param string $path
     * @param string $protocol
     * @param null|string $host
     * @param null|string $language
     * @return string
     */
    public function url($path, $protocol = 'http', $host = null, $language = null)
    {
        if ( is_null($host) )
        {
            $host = $_SERVER['HTTP_HOST'];
        }

	    if ($language == null)
	    {
		    return $protocol . '://' . $host . $path;
	    }

	    return $protocol . '://' . $host . '/' . $language . $path;
    }

}
