<?php

namespace App\Traits;

use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Cache;

trait OtpTrait
{

    public function generateOtp(
        $email,
        $ipAddress,
        $type = 'alpha_numeric',
        $length = 6,
        $validity = 2,
    ): mixed {
        $otp = new Otp;
        $otp = $otp->generate(
            $email,
            $type,
            $length,
            $validity
        );
        $cache = Cache::store('database');
        $cache->put($ipAddress, [$email, $otp->token]);
        return $otp->token;
    }
}
