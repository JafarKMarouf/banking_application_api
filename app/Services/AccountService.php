<?php

namespace App\Services;

use App\Dtos\AccountDto;
use App\Dtos\DepositDto;
use App\Dtos\TransactionDto;
use App\Dtos\UserDto;
use App\Events\DepositEvent;
use App\Exceptions\AccountNumberExistsException;
use App\Exceptions\AmountToLowDepositException;
use App\Exceptions\InvaildAccountNumberException;
use App\Interfaces\AccountServiceInterface;
use App\Models\Account;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;

class AccountService implements AccountServiceInterface
{

    public function __construct(
        private readonly UserService $userService,
        private readonly TransactionService $transactionService
    ) {}

    /**
     * @inheritDoc
     */
    public function createAccountNumber(UserDto $userDto): Account
    {
        if ($this->hasAccountNumber($userDto)) {
            throw new AccountNumberExistsException();
        }
        $randomDigits = rand(1000, 9999);
        $mixedAccountNumber =
            substr(
                $userDto->getPhoneNumber(),
                5,
                10
            ) . substr(
                $userDto->getPhoneNumber(),
                0,
                6
            ) . $randomDigits;

        $account = $this->modelQuery()->create([
            'account_number' => $mixedAccountNumber,
            'user_id' => $userDto->getId(),
        ]);
        return $account;
    }

    /**
     * @inheritDoc
     */
    public function getAccount(int|string $accountNumberOrUserId): Account
    {
        dd($accountNumberOrUserId);
    }

    /**
     * @inheritDoc
     */
    public function getAccountByAccountNumber(string $accountNumber): Account
    {
        $account =  $this->modelQuery()
            ->where('account_number', $accountNumber)
            ->first();
        return $account;
    }

    /**
     * @inheritDoc
     */
    public function getAccountByUserId(int $userId): Account
    {
        $account = $this->modelQuery()
            ->where('user_id', $userId)
            ->first();

        return $account;
    }

    /**
     * @inheritDoc
     */
    public function modelQuery(): Builder
    {
        return Account::query();
    }
    /**
     * @inheritDoc
     */
    public function hasAccountNumber(UserDto $userDto): bool
    {
        return $this->modelQuery()
            ->where('user_id', $userDto->getId())
            ->exists();
    }
    /**
     * @inheritDoc
     */
    public function deposit(DepositDto $depositDto): void
    {
        // dd($depositDto);
        $minimun_amount  = 500;
        if ($depositDto->getAmount() < $minimun_amount) {
            throw new AmountToLowDepositException($minimun_amount);
        }

        try {
            DB::beginTransaction();
            $accountQuery = $this->modelQuery()->where(
                'account_number',
                $depositDto->getAccount_number()
            );
            $this->accountExist($accountQuery);
            $lockedAccount = $accountQuery->lockForUpdate()->first();
            $accountDto = AccountDto::fromModel($lockedAccount);
            $transactionDto = TransactionDto::forDeposit(
                $accountDto,
                $this->transactionService->generateReference(),
                $depositDto->getAmount(),
                $depositDto->getDescription(),
            );
            event(new DepositEvent(
                $transactionDto,
                $accountDto,
                $lockedAccount
            ));
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }

    /**
     * @param \Illuminate\Database\Eloquent\Builder $accountQuery
     * @throws \App\Exceptions\InvaildAccountNumberException
     * @return void
     */
    public function accountExist(Builder $accountQuery): void
    {
        if ($accountQuery->exists() == false) {
            throw new InvaildAccountNumberException();
        }
    }
}
