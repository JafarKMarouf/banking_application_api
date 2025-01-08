<?php

namespace App\Services;

use App\Dtos\UserDto;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UserService
{
    public function createUser(UserDto $userDto)
    {
        return User::create([
            'name' => $userDto->getName(),
            'email' => $userDto->getEmail(),
            'phone_number' => $userDto->getPhoneNumber(),
            'password' => $userDto->getPassword(),
        ]);
    }
}
