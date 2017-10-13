<?php

namespace Rasulian\UserVerification\Exceptions;

use Exception;

class UserNotVerifiedException extends Exception
{
    /**
     * The exception description.
     *
     * @var string
     */
    protected $message = 'This user is not verified.';
}