<?php

namespace Inetstudio\Instagram\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * Class InstagramPostFacade.
 */
class InstagramPostFacade extends Facade
{
    /**
     * @return string
     */
    protected static function getFacadeAccessor()
    {
        return 'InstagramPost';
    }
}