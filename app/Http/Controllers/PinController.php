<?php

namespace App\Http\Controllers;

use App\Http\Requests\SetupPinRequest;
use App\Http\Response\Response;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PinController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    // public function __construct(private readonly UserService $userService) {}
    /**
     * @param \App\Http\Requests\SetupPinRequest $request
     * @return JsonResponse
     */
    public function setupPin(SetupPinRequest $request, UserService $userService): JsonResponse
    {
        $request->validated();
        $user = $request->user();
        $userService->setupPin($user, $request->input('pin'));
        return Response::success([], 'Pin is setup Successfully');
    }


    public function validatePin(Request $request, UserService $userService): JsonResponse
    {
        $this->validate($request, [
            'pin' => ['required', 'string'],
        ]);

        $user = $request->user();
        $is_valid = $userService->validatePin($user->id, $request->input('pin'));
        return  Response::success([
            'is_valid' => $is_valid,
        ], "Pin validated Successfully");
    }

    public function hasSetPIN(Request $request, UserService $userService): JsonResponse
    {
        $user = $request->user();
        $data = $userService->hasSetPin($user);

        return Response::success([
            'note' => $data ? 'You have set your PIN.' : 'You have not set PIN yet!'
        ], 'PIN check Successfully');
    }
}
