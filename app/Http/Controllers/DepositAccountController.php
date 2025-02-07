<?php

namespace App\Http\Controllers;

use App\Dtos\DepositDto;
use App\Http\Requests\DepositRequest;
use App\Http\Response\Response;
use App\Services\AccountService;
use Illuminate\Http\JsonResponse;

class DepositAccountController extends Controller
{
    public function __construct(private readonly AccountService $accountService) {}

    public function store(DepositRequest $depositRequest): JsonResponse
    {
        $depositRequest->validated();
        $depositDto = DepositDto::fromApiFormRequest($depositRequest);
        $this->accountService->deposit($depositDto);
        return Response::success([], 'Deposit Successfully');
    }
}
