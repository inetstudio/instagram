<?php

namespace Inetstudio\Instagram\Facades;

use Illuminate\Support\Facades\Facade;

class InstagramPostFacade extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'InstagramPost';
    }
}