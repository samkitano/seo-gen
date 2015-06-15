<?php namespace Samkitano\SeoGen\Facades;

use Illuminate\Support\Facades\Facade;

class Sitemap extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'SeoGen.sitemap';
    }

}
