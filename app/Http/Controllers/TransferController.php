<?php

namespace App\Http\Controllers;

use App\Exceptions\AmountToLowException;
use App\Exceptions\InvalidAccountNumberException;
use App\Exceptions\InvalidPinException;
use App\Exceptions\NotEnoughBalanceException;
use App\Exceptions\NotSetupPin;
use App\Http\Requests\StoreTransferRequest;
use App\Http\Response\Response;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class TransferController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
    ) {}

    /**
     * @throws NotSetupPin
     * @throws AmountToLowException
     * @throws InvalidPinException
     * @throws InvalidAccountNumberException
     * @throws NotEnoughBalanceException
     */
    public function store(StoreTransferRequest $request): JsonResponse
    {
        $request->validated();
        $user = $request->user();

        $senderAccount = $this->accountService->getAccountByUserId($user->id);
        $this->accountService->transfer(
            $senderAccount->account_number,
            $request->input('recipient_account_number'),
            $request->input('pin'),
            $request->input('amount'),
            $request->input('description')
        );
        return Response::sendSuccess([], 'Transfer successful');
    }
}
