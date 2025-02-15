<?php

namespace App\Http\Controllers;

use App\Dtos\WithdrawDto;
use App\Exceptions\AmountToLowException;
use App\Exceptions\InvalidAccountNumberException;
use App\Exceptions\InvalidPinException;
use App\Exceptions\NotEnoughBalanceException;
use App\Exceptions\NotSetupPin;
use App\Http\Requests\StoreWithdrawRequest;
use App\Http\Response\Response;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class WithdrawAccountController extends Controller
{
    public function __construct(private readonly AccountService $accountService) {}

    /**
     * @throws NotSetupPin
     * @throws AmountToLowException
     * @throws InvalidPinException
     * @throws InvalidAccountNumberException
     * @throws NotEnoughBalanceException
     */
    public function store(StoreWithdrawRequest $withdrawRequest): JsonResponse
    {
        $withdrawRequest->validated();
        $account = $this->accountService->getAccountByUserId($withdrawRequest->user()->id);

        $withdrawDto = new WithdrawDto();

        $withdrawDto->setAccountNumber($account->account_number);
        $withdrawDto->setPin($withdrawRequest->input('pin'));
        $withdrawDto->setAmount($withdrawRequest->input('amount'));
        $withdrawDto->setDescription($withdrawRequest->input('description'));

        $this->accountService->withdraw($withdrawDto);

        return Response::sendSuccess([], 'Withdraw Successfully');
    }
}
