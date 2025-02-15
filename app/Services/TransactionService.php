<?php

namespace App\Services;

use App\Dtos\AccountDto;
use App\Dtos\TransactionDto;
use App\Enums\TransactionCategoryEnum;
use App\Exceptions\ANotFoundException;
use App\Interfaces\TransactionServiceInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use App\Models\Transaction;

class TransactionService implements TransactionServiceInterface
{

    public function modelQuery(): Builder
    {
        return Transaction::query();
    }
    public function createTransaction(TransactionDto $transactionDto): Transaction
    {
        $data = [];
        if ($transactionDto->getCategory() == TransactionCategoryEnum::DEPOSIT->value) {
            $data = $transactionDto->forDepositToArray($transactionDto);
        }
        if ($transactionDto->getCategory() == TransactionCategoryEnum::WITHDRAW->value) {
            $data = $transactionDto->forWithdrawToArray($transactionDto);
        }

        /** @var Transaction $transaction */
        $transaction = $this->modelQuery()->create($data);

        return $transaction;
    }

    public function generateReference(): string
    {
        return Str::upper(
            'TF' . '/' . Carbon::now()->getTimestampMs() . '/' . Str::random(4)
        );
    }

    /**
     * @throws ANotFoundException
     */
    public function getTransactionById(int $id): Transaction
    {
        $transaction = $this->modelQuery()->where('id', $id)->first();

        if (!$transaction) {
            throw new ANotFoundException('transaction with the supplied reference does not exist');
        }
        /** @var Transaction $transaction */
        return $transaction;
    }

    /**
     * @throws ANotFoundException
     */
    public function getTransactionByReference(string $reference): Transaction
    {
        /** @var Transaction $transaction*/
        $transaction = $this->modelQuery()
            ->where('reference', $reference)->first();
        if (!$transaction) {
            throw new ANotFoundException('transaction with the supplied id does not exist');
        }
        return $transaction;
    }

    public function downloadTransactionHistory(
        AccountDto $accountDto,
        Carbon $fromDate,
        Carbon $toDate
    ): Collection {
        return Collection::empty();
    }

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
    public function getTransactionByAccountNumber(string $accountNumber, Builder $builder): Builder
    {
        return $builder->whereHas('account', function ($query) use ($accountNumber): void {
            $query->where('account_number', $accountNumber);
        });
    }

    public function getTransactionByUserId(int $userId, Builder $builder): Builder
    {
        return $builder->where('user_id', $userId);
    }
}
