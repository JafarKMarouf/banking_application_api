<?php

namespace App\Http\Controllers;

use App\Dtos\UserDto;
use App\Http\Requests\RegisterUserRequest;
use App\Services\UserService;

class AuthController extends Controller
{
    protected $property;

    public function __construct(public readonly UserService $userService) {}


    public function register(RegisterUserRequest $request)
    {


        $userDto = UserDto::fromApiFormRequest($request);
        $user = $this->userService->createUser($userDto);

        return response()->json([
            'success' => true,
            'message' => 'Registeration Successfull',
            'user' => $user,
        ]);
    }
}
