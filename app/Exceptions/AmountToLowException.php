<?php

namespace App\Exceptions;

use Exception;

class AmountToLowException extends Exception
{
    /**
     * @param string $message
     * @param int $status_code
     */
    public function __construct(string $message = "", int $status_code)
    {
        parent::__construct($message, $status_code);
    }
}
