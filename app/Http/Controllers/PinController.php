<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidPinException;
use App\Exceptions\NotSetupPin;
use App\Exceptions\PinHasAlreadyBeenSet;
use App\Http\Requests\SetupPinRequest;
use App\Http\Response\Response;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PinController extends Controller
{
    public function __construct(private readonly UserService $userService) {}

    /**
     * @param SetupPinRequest $request
     * @param UserService $userService
     * @return JsonResponse
     * @throws PinHasAlreadyBeenSet
     */
    public function setupPin(SetupPinRequest $request, UserService $userService): JsonResponse
    {
        $request->validated();
        $user = $request->user();
        $userService->setupPin($user, $request->input('pin'));
        return Response::sendSuccess([], 'Pin is setup Successfully');
    }


    /**
     * @throws NotSetupPin
     * @throws InvalidPinException
     * @throws ValidationException
     */
    public function validatePin(Request $request, UserService $userService): JsonResponse
    {
        $this->validate($request, [ 'pin' => ['required', 'string'] ]);

        $user = $request->user();
        $is_valid = $userService->validatePin($user->id, $request->input('pin'));
        return  Response::sendSuccess([
            'is valid' => $is_valid,
        ], "Pin validated Successfully");
    }

    public function hasSetPIN(Request $request, UserService $userService): JsonResponse
    {
        $user = $request->user();
        $data = $userService->hasSetPin($user);

        return Response::sendSuccess([
            'note' => $data ? 'You have set your PIN.' : 'You have not set PIN yet!'
        ], 'PIN check Successfully');
    }
}
