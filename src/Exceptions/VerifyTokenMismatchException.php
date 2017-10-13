<?php

namespace Rasulian\UserVerification\Exceptions;

use Exception;

class VerifyTokenMismatchException extends Exception
{
    /**
     * The exception description.
     *
     * @var string
     */
    protected $message = 'Wrong verification token/code.';
}