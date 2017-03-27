<?php

namespace Inetstudio\Instagram\Facades;

use Illuminate\Support\Facades\Facade;

class InstagramUserFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'InstagramUser';
    }
}