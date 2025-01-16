<?php

namespace App\Http\Controllers;

use App\Dtos\UserDto;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Response\Response;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
            $userDto = UserDto::fromApiFormRequest($request->validated());
            $data = $this->userService->createUser($userDto);
            return Response::success($data['user'], $data['message']);
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }
}
