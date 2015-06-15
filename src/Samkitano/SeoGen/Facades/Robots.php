<?php namespace Samkitano\SeoGen\Facades;

use Illuminate\Support\Facades\Facade;

class Robots extends Facade {

    protected static function getFacadeAccessor()
    {
        return 'SeoGen.robots';
    }

}
