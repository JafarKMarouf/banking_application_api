<?php

namespace App\Http\Controllers;

use App\Dtos\UserDto;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Response\Response;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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
        $request->validated();
        $userDto = UserDto::fromApiFormRequest($request);
        $ipAddress = $request->ip();
        $response = $this->userService->createUser($userDto, $ipAddress);

        return Response::success(
            $response['data'],
            $response['message'],
            201
        );
    }

    public function login(LoginUserRequest $request): JsonResponse
    {
        $request->validated();
        $response = $this->userService->loginUser($request);
        return $response['code'] != 200 ?
            Response::error(
                $response['message'],
                $response['code']
            ) : Response::success(
                $response['data'],
                $response['message']
            );
    }

    public function logout(): JsonResponse
    {
        $userId = auth()->user()->id;
        User::find($userId)->tokens()->delete();
        return Response::success([], 'User Logged out Successfully');
    }

    public function user(Request $request): JsonResponse
    {
        $user = auth()->user();
        return Response::success(
            $user,
            'Authenticated User retrieved'
        );
    }
}
