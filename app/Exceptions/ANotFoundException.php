<?php

namespace App\Exceptions;

use Exception;

class ANotFoundException extends Exception
{
    public function __construct(string $message = "", int $code = 0, \Throwable $previous = null)
    {
        parent::__construct($message, 404);
    }
}
