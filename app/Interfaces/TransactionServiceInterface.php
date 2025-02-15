<?php

namespace App\Interfaces;

use App\Dtos\AccountDto;
use App\Dtos\TransactionDto;
use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

interface TransactionServiceInterface
{
    public function modelQuery(): Builder;

    public function generateReference(): string;

    public function createTransaction(TransactionDto $transactionDto): Transaction;
    public function getTransactionByReference(string $reference): Transaction;
    public function getTransactionById(int $id): Transaction;
    public function getTransactionByAccountNumber(string $accountNumber, Builder $builder): Builder;
    public function getTransactionByUserId(int $userId, Builder $builder): Builder;
    public function downloadTransactionHistory(
        AccountDto $accountDto,
        Carbon $fromDate,
        Carbon $toDate
    ): Collection;

    public function updateTransactionBalance(string $reference, float|int $balance): void;
    public function updateTransferId(string $reference, int $transferId): void;
}
