<?php

namespace App\Http\Controllers;

use App\Dtos\UserDto;
use App\Http\Response\Response;
use App\Services\AccountService;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function __construct(private readonly AccountService $accountService) {}

    public function getAccount($identifier)
    {
        $this->accountService->getAccount($identifier);
    }

    public function createAccountNumber(Request $request)
    {
        $userDto =  UserDto::fromModel($request->user());
        $data = $this->accountService->createAccountNumber($userDto);
        $message = 'Account number generated Successfully!';
        return Response::success($data, $message);
    }
}
