<?php

namespace App\Services;

use App\Dtos\UserDto;
use App\Models\User;
use App\Notifications\SendEmailNotification;

class UserService
{
    public function createUser(UserDto $userDto)
    {
        $user =
            User::query()->create([
                'name' => $userDto->getName(),
                'email' => $userDto->getEmail(),
                'phone_number' => $userDto->getPhoneNumber(),
                'password' => $userDto->getPassword(),
            ]);

        $user->notify(new SendEmailNotification('12343'));
        $message = 'User Registeration Successfull!';
        return ['user' => $user, 'message' => $message];
    }
}
