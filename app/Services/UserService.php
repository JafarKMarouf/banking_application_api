<?php

namespace App\Services;

use App\Dtos\UserDto;
use App\Jobs\SendEmailJob;
use App\Jobs\SendEmailVerificationJob;
use App\Jobs\SendWelcomeJob;
use App\Models\User;
use App\Traits\OtpTrait;
use Illuminate\Http\Request;
use \App\Notifications\SendEmailVerificationNotification;

class UserService
{
    use OtpTrait;
    public function createUser(Request $request, UserDto $userDto): array
    {
        $user =
            User::query()->create([
                'name' => $userDto->getName(),
                'email' => $userDto->getEmail(),
                'phone_number' => $userDto->getPhoneNumber(),
                'password' => $userDto->getPassword(),
            ]);
        $token = $user->createToken('token')->plainTextToken;

        $otp = $this->generateOtp($request, $user->email);
        $user->notify(new SendEmailVerificationNotification($otp));

        dispatch(new SendWelcomeJob($user));

        $data['user'] = $user;
        $data['token'] = $token;

        $message = 'User Registeration Successfull!';

        return ['data' => $data, 'message' => $message];
    }
}
