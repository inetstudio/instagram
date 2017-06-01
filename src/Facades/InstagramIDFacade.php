<?php

namespace Inetstudio\Instagram\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class InstagramIDFacade.
 */
class InstagramIDFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'InstagramID';
    }
}
