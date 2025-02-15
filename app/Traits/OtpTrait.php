<?php

namespace App\Traits;

use Exception;
use Ichtrojan\Otp\Otp;
use Illuminate\Support\Facades\Cache;

trait OtpTrait
{

    /**
     * @param $email
     * @param $ipAddress
     * @param string $type
     * @param int $length
     * @param int $validity
     * @return mixed
     * @throws Exception
     */
    public function generateOtp(
        $email,
        $ipAddress,
        string $type = 'alpha_numeric',
        int $length = 6,
        int $validity = 2,
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
