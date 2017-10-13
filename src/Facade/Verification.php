<?php

namespace Rasulian\UserVerification\Facade;

use Illuminate\Support\Facades\Facade;

class Verification extends Facade
{
    protected static function getFacadeAccessor()
    {
        return 'user.verification';
    }
}
