<?php

namespace App\Http\Controllers;

use App\Dtos\UserDto;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Http\Response\Response;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class AuthController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    /**
     *
     * @param RegisterUserRequest $request
     * @return JsonResponse
     */
    public function register(RegisterUserRequest $request): JsonResponse
    {
        $request->validated();
        $userDto = UserDto::fromApiFormRequest($request);
        $ipAddress = $request->ip();

        /** @var UserDto $userDto*/
        $response = $this->userService->createUser($userDto, $ipAddress);

        return Response::sendSuccess(
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
            Response::sendError(
                $response['message'],
                $response['code']
            ) : Response::sendSuccess(
                $response['data'],
                $response['message']
            );
    }

    public function logout(): JsonResponse
    {
        $user = request()->user();
        $user->tokens()->delete();
        return Response::sendSuccess([], 'User Logged out Successfully');
    }

    public function user(): JsonResponse
    {
        $user = request()->user();
        return Response::sendSuccess(
            $user,
            'Authenticated User retrieved'
        );
    }
}
