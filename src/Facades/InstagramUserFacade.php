<?php

namespace Inetstudio\Instagram\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class InstagramUserFacade.
 */
class InstagramUserFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'InstagramUser';
    }
}
