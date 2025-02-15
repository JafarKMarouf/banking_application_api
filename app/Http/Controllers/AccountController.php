<?php

namespace App\Http\Controllers;

use App\Dtos\UserDto;
use App\Exceptions\AccountNumberExistsException;
use App\Http\Response\Response;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private readonly AccountService $accountService) {}

    /**
     * @throws AccountNumberExistsException
     */
    public function createAccountNumber(Request $request): JsonResponse
    {
        /** @var UserDto $userDto*/
        $userDto =  UserDto::fromModel($request->user());
        $data = $this->accountService->createAccountNumber($userDto);
        $message = 'Account number generated Successfully!';
        return Response::sendSuccess($data, $message);
    }
}
