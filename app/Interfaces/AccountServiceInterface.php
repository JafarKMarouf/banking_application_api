<?php

namespace App\Interfaces;

use App\Dtos\AccountDto;
use App\Dtos\DepositDto;
use App\Dtos\UserDto;
use App\Dtos\WithdrawDto;
use App\Models\Account;
use Illuminate\Database\Eloquent\Builder;

interface AccountServiceInterface
{
    public function modelQuery(): Builder;

    public function createAccountNumber(UserDto $userDto): Account;

    public function getAccountByAccountNumber(string $accountNumber): Account;

    public function getAccountByUserId(int $userId): Account;

    public function getAccount(int|string $accountNumberOrUserId): Account;

    public function hasAccountNumber(UserDto $userDto): bool;

    public function accountExist(Builder $accountQuery): void;

    public function deposit(DepositDto $depositDto): void;

    public function withdraw(WithdrawDto $withdrawDto): void;

    public function canWithdraw(AccountDto $accountDto, WithdrawDto $withdrawDto): bool;
}
