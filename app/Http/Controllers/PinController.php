<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetupPinRequest;
use App\Http\Response\Response;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;

class PinController extends Controller
{
    private UserService $userService;
    public function __construct(UserService $userService)
    {
        $this->userService = $userService;
    }

    // public function __construct(private readonly UserService $userService) {}
    /**
     * @param \App\Http\Requests\SetupPinRequest $request
     * @return JsonResponse
     */
    public function setupPin(SetupPinRequest $request): JsonResponse
    {
        $request->validated();
        $user = $request->user();
        $this->userService->setupPin($user, $request);
        return Response::success([], 'Pin is setup Successfully');
    }
}
