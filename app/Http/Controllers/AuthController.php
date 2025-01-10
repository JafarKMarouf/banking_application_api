<?php

namespace App\Http\Controllers;

use App\Dtos\UserDto;
use App\Http\Requests\RegisterUserRequest;
use App\Services\UserService;
use App\Traits\ApiResponseTrait;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    protected $property;

    public function __construct(public readonly UserService $userService) {}


    public function register(RegisterUserRequest $request): JsonResponse
    {
        $userDto = UserDto::fromApiFormRequest($request);
        $user = $this->userService->createUser($userDto);
        return $this->sendSuccess(
            ['user' => $user],
            'Registeration Successfully'
        );
    }
}
