<?php

namespace App\Interfaces;

use App\Dtos\AccountDto;
use App\Dtos\TransactionDto;
use App\Models\Transcation;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder as EloquentBuilder;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface TransactionServiceInterface
{
    public function modelQuery(): Builder;

    public function generateReference(): string;

    public function createTransaction(TransactionDto $transactionDto): Transcation;
    public function getTransactionByReference(string $reference): Transcation;
    public function getTransactionById(int $id): Transcation;

    public function getTransactionByAccountNumber(string $accountNumber): Builder;
    public function getTransactionByUserId(int $userId): Builder;

    public function downloadTransactionHistory(
        AccountDto $accountDto,
        Carbon $fromDate,
        Carbon $toDate
    ): Collection;

    public function updateTransactionBalance(string $reference, float|int $balance): void;
    public function updateTransferId(string $reference, int $transferId): void;
}
