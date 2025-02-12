<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTransferRequest;
use App\Http\Response\Response;
use App\Services\AccountService;

class TransferController extends Controller
{
    public function __construct(
        private readonly AccountService $accountService,
    ) {}

    public function store(StoreTransferRequest $request)
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
        return Response::success([], 'Transfer successfull');
    }
}
