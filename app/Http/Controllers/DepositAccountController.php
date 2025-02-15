<?php

namespace App\Http\Controllers;

use App\Dtos\DepositDto;
use App\Exceptions\AmountToLowException;
use App\Exceptions\InvalidAccountNumberException;
use App\Http\Requests\StoreDepositRequest;
use App\Http\Response\Response;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class DepositAccountController extends Controller
{
    public function __construct(private readonly AccountService $accountService) {}

    /**
     * @throws InvalidAccountNumberException
     * @throws AmountToLowException
     */
    public function store(StoreDepositRequest $depositRequest): JsonResponse
    {
        $depositRequest->validated();

        $depositDto = DepositDto::fromApiFormRequest($depositRequest);

        /** @var DepositDto $depositDto */
        $this->accountService->deposit($depositDto);

        return Response::sendSuccess([], 'Deposit Successfully');
    }
}
