<?php namespace Samkitano\SeoGen;

use Samkitano\SeoGen\Interfaces\HelpersInterface;

class Generator {

	/**
	 * @var HelpersInterface
	 */
	protected $helpers;

	protected $protocol;
	protected $host;
	protected $field_names;

	protected $lang_file;
	protected $languages;
	protected $default_lang;


	/**
	 * @param        $helpers
	 * @param string $protocol
	 * @param null   $host
	 * @param array  $field_names
	 * @param string $lang_file
	 * @param array  $languages
	 * @param string $default_lang
	 */
	function __construct(
							$helpers,
							$protocol       = 'http',
							$host           = null,
							$field_names    = array(),
							$lang_file      = 'routes',
							$languages      = array(),
							$default_lang   = 'en'
	)
	{
		$this->helpers      = $helpers;
		$this->protocol     = $protocol;
		$this->host         = $host;
		$this->field_names  = $field_names;
		$this->lang_file    = $lang_file;
		$this->languages    = $languages;
		$this->default_lang = $default_lang;
	}

}
