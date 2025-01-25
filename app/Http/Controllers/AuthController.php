<?php

namespace App\Http\Controllers;

use App\Dtos\UserDto;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Response\Response;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    private UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     *
     * @param \App\Http\Requests\RegisterUserRequest $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        try {
            $request->validated();
            $userDto = UserDto::fromApiFormRequest($request);
            $ipAddress = $request->ip();
            $response = $this->userService->createUser($userDto, $ipAddress);

            return Response::success(
                $response['data'],
                $response['message'],
                201
            );
        } catch (\Throwable $th) {
            return Response::error($th->getMessage());
        }
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        try {
            $request->validated();
            $response = $this->userService->loginUser($request);
            return Response::success(
                $response['data'],
                $response['message']
            );
        } catch (\Throwable $th) {
            return Response::error(
                $th->getMessage(),
                $th->getCode(),
            );
        }
    }

    public function logout(): JsonResponse
    {
        $userId = auth()->user()->id;
        User::find($userId)->tokens()->delete();
        return Response::success([], 'User Logged out Successfully');
    }
}
