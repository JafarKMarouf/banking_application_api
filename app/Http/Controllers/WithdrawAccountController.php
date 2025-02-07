<?php

namespace App\Http\Controllers;

use App\Dtos\WithdrawDto;
use App\Http\Requests\WithdrawRequest;
use App\Http\Response\Response;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class WithdrawAccountController extends Controller
{
    public function __construct(private readonly AccountService $accountService) {}

    public function store(WithdrawRequest $withdrawRequest): JsonResponse
    {
        $withdrawRequest->validated();
        $account = $this->accountService->getAccountByUserId($withdrawRequest->user()->id);

        $withdrawDto = new WithdrawDto();
        $withdrawDto->setAccountNumber($account->account_number);
        $withdrawDto->setPin($withdrawRequest->input('pin'));
        $withdrawDto->setAmount($withdrawRequest->input('amount'));
        $withdrawDto->setDescription($withdrawRequest->input('description'));
        // dd($withdrawDto);
        $this->accountService->withdraw($withdrawDto);
        return Response::success([], 'Withdraw Successfully');
    }
}
