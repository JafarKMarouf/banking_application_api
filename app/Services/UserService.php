<?php

namespace App\Services;

use App\Dtos\UserDto;
use App\Http\Response\Response;
use App\Jobs\SendEmailVerificationJob;
use App\Jobs\SendWelcomeJob;
use App\Models\User;
use App\Traits\OtpTrait;
use \App\Notifications\SendEmailVerificationNotification;
use Exception;
use Illuminate\Support\Facades\Auth;

class UserService
{
    use OtpTrait;
    public function createUser(UserDto $userDto, $ipAddress): array
    {
        $user =
            User::query()->create([
                'name' => $userDto->getName(),
                'email' => $userDto->getEmail(),
                'phone_number' => $userDto->getPhoneNumber(),
                'password' => $userDto->getPassword(),
            ]);

        $token = $user->createToken('token')->plainTextToken;
        $otp = $this->generateOtp($user->email, $ipAddress);

        dispatch(new SendEmailVerificationJob($user, $otp));

        $data['user'] = $user;
        $data['token'] = $token;

        $message = 'User Registeration Successfull!';

        return ['data' => $data, 'message' => $message];
    }

    public function loginUser($request): array
    {
        $field = filter_var(
            $request->identifier,
            FILTER_VALIDATE_EMAIL
        ) ? 'email' : 'phone_number';
        if (!Auth::attempt([
            $field => $request->identifier,
            'password' => $request->password
        ])) {
            $data = [];
            $message = "User $field & password does not match with our record.";
            $code = 401;
        } else {
            $user = $request->user();
            $data['user'] = $user;
            $data['token'] = $user->createToken('token')->plainTextToken;

            $message = 'User Logged In Successfully!';
            $code = 200;
        }
        return ['data' => $data, 'message' => $message, 'code' => $code];
    }
}
