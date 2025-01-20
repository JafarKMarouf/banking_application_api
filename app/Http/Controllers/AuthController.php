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
            $request->validated();
            $userDto = UserDto::fromApiFormRequest($request);
            $response = $this->userService->createUser($request, $userDto);

            return Response::success(
                $response['data'],
                $response['message'],
                201
            );
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }
}
