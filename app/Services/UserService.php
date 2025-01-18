<?php

namespace App\Services;

use App\Dtos\UserDto;
use App\Models\User;
use App\Notifications\SendEmailNotification;
use App\Traits\OtpTrait;

class UserService
{
    use OtpTrait;
    public function createUser(UserDto $userDto)
    {
        $user =
            User::query()->create([
                'name' => $userDto->getName(),
                'email' => $userDto->getEmail(),
                'phone_number' => $userDto->getPhoneNumber(),
                'password' => $userDto->getPassword(),
            ]);
        $otp = $this->generateOtp($user->email);

        $user->notify(new SendEmailNotification($otp));
        $message = 'User Registeration Successfull!';
        return ['user' => $user, 'message' => $message];
    }
}
