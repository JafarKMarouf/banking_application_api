<?php

namespace App\Exceptions;

use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

use Exception;

class DepositAmountToLowException extends Exception
{
    public function __construct($amount) {}
}
