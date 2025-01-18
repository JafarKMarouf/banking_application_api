<?php

namespace App\Traits;

use Ichtrojan\Otp\Otp;

trait OtpTrait
{
    public function generateOtp(
        $email,
        $type = 'alpha_numeric',
        $length = 6,
        $validity = 2
    ): mixed {
        $otp = new Otp;

        $otp = $otp->generate(
            $email,
            $type,
            $length,
            $validity
        );

        return $otp->token;
    }
}
