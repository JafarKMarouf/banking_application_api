<?php

namespace App\Services;

use App\Dtos\UserDto;
use App\Exceptions\AccountNumberExistsException;
use App\Interfaces\AccountServiceInterface;
use App\Models\Account;
use Illuminate\Database\Eloquent\Builder;

class AccountService implements AccountServiceInterface
{

    /**
     * @inheritDoc
     */
    public function createAccountNumber(UserDto $userDto): Account
    {
        // dd($this->hasAccountNumber($userDto));
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
}
