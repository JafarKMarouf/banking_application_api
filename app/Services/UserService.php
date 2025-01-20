<?php

namespace App\Services;

use App\Dtos\UserDto;
use App\Models\User;
use App\Notifications\SendEmailNotification;
use App\Traits\OtpTrait;
use Illuminate\Http\Request;

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
        $user->notify(new SendEmailNotification($otp));

        $data['user'] = $user;
        $data['token'] = $token;

        $message = 'User Registeration Successfull!';
        return ['data' => $data, 'message' => $message];
    }
}
