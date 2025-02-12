<?php

namespace App\Services;

use App\Dtos\AccountDto;
use App\Dtos\TransactionDto;
use App\Enums\TransactionCategoryEnum;
use App\Interfaces\TransactionServiceInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use \Illuminate\Support\Str;
use App\Models\Transcation;

class TransactionService implements TransactionServiceInterface
{
    /**
     * @inheritDoc
     */
    public function modelQuery(): Builder
    {
        return Transcation::query();
    }
    /**
     * @inheritDoc
     */
    public function createTransaction(TransactionDto $transactionDto): Transcation
    {
        $data = [];
        if ($transactionDto->getCategory() == TransactionCategoryEnum::DEPOSIT->value) {
            $data = $transactionDto->forDepositToArray($transactionDto);
        }
        if ($transactionDto->getCategory() == TransactionCategoryEnum::WITHDRAW->value) {
            $data = $transactionDto->forWithdrawToArray($transactionDto);
        }
        $transaction = $this->modelQuery()->create($data);
        return $transaction;
    }

    /**
     * @inheritDoc
     */
    public function generateReference(): string
    {
        return Str::upper(
            'TF' . '/' . Carbon::now()->getTimestampMs() . '/' . Str::random(4)
        );
    }

    /**
     * @inheritDoc
     */
    public function getTransactionByAccountNumber(string $account_number): Builder {}

    /**
     * @inheritDoc
     */
    public function getTransactionById(int $id): Transcation {}

    /**
     * @inheritDoc
     */
    public function getTransactionByReference(string $reference): Transcation {}
    /**
     * @inheritDoc
     */
    public function getTransactionByUserId(int $userId): Builder {}
    /**
     * @inheritDoc
     */
    public function downloadTransactionHistory(
        AccountDto $accountDto,
        Carbon $fromDate,
        Carbon $toDate
    ): Collection {}

    public function updateTransactionBalance(string $reference, float|int $balance): void
    {
        $this->modelQuery()->where('reference', $reference)
            ->update([
                'balance' => $balance,
                'confirmed' => true,
            ]);
    }

    public function updateTransferId(string $reference, int $transferId): void
    {
        $this->modelQuery()->where('reference', $reference)
            ->update([
                'transfer_id' => $transferId,
            ]);
    }
}
