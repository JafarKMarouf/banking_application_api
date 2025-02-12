<?php

namespace App\Services;

use App\Dtos\AccountDto;
use App\Dtos\DepositDto;
use App\Dtos\TransactionDto;
use App\Dtos\TransferDto;
use App\Dtos\UserDto;
use App\Dtos\WithdrawDto;
use App\Events\TransactionEvent;
use App\Exceptions\AccountNumberExistsException;
use App\Exceptions\AmountToLowException;
use App\Exceptions\InvaildAccountNumberException;
use App\Exceptions\InvaildPinException;
use App\Exceptions\NotEnoughBalanceException;
use App\Interfaces\AccountServiceInterface;
use App\Models\Account;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\Response as HttpFoundationResponse;

class AccountService implements AccountServiceInterface
{

    public function __construct(
        private readonly UserService $userService,
        private readonly TransactionService $transactionService,
        private readonly TransferService $transferService,
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
    public function canWithdraw(AccountDto $accountDto, WithdrawDto $withdrawDto): bool
    {
        if ($accountDto->getBalance() < $withdrawDto->getAmount()) {
            throw new NotEnoughBalanceException();
        }

        return true;
    }
    /**
     * @inheritDoc
     */
    public function vaildAccountNumber(string $account_number): void
    {
        $user_id = auth()->user()->id;
        $account = $this->getAccountByUserId(userId: $user_id);
        if ($account->account_number != $account_number) {
            throw new InvaildAccountNumberException();
        }
    }

    /**
     * @inheritDoc
     */
    public function deposit(DepositDto $depositDto): void
    {
        $minimun_amount  = 500;
        if ($depositDto->getAmount() < $minimun_amount) {
            throw new AmountToLowException(
                "amount must be equal or greater than $minimun_amount",
                HttpFoundationResponse::HTTP_BAD_REQUEST
            );
        }
        try {
            DB::beginTransaction();
            $accountQuery = $this->modelQuery()->where(
                'account_number',
                $depositDto->getAccount_number()
            );

            $this->accountExist($accountQuery);
            $this->vaildAccountNumber($depositDto->getAccount_number());

            $user_id = auth()->user()->id;
            $account = $this->getAccountByUserId($user_id);
            if ($account->account_number != $depositDto->getAccount_number()) {
                throw new InvaildAccountNumberException();
            }
            $lockedAccount = $accountQuery->lockForUpdate()->first();
            $accountDto = AccountDto::fromModel($lockedAccount);
            $transactionDto = TransactionDto::forDeposit(
                $accountDto,
                $this->transactionService->generateReference(),
                $depositDto->getAmount(),
                $depositDto->getDescription(),
            );
            // dd($transactionDto);
            event(new TransactionEvent(
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
     * @inheritDoc
     */
    public function withdraw(WithdrawDto $withdrawDto): void
    {
        $minimun_amount  = 500;
        if ($withdrawDto->getAmount() < $minimun_amount) {
            throw new AmountToLowException(
                "amount must be equal or greater than $minimun_amount",
                HttpFoundationResponse::HTTP_BAD_REQUEST
            );
        }
        try {
            DB::beginTransaction();
            $accountQuery = $this->modelQuery()->where(
                'account_number',
                $withdrawDto->getAccountNumber()
            );
            $this->accountExist($accountQuery);
            $lockedAccount = $accountQuery->lockForUpdate()->first();

            $accountDto = AccountDto::fromModel($lockedAccount);
            if (!$this->userService->validatePin(
                $accountDto->getUserId(),
                $withdrawDto->getPin()
            )) {
                throw new InvaildPinException();
            }
            $this->canWithdraw($accountDto, $withdrawDto);
            $transactionDto = TransactionDto::forWithdraw(
                $accountDto,
                $this->transactionService->generateReference(),
                $withdrawDto->getAmount(),
                $withdrawDto->getDescription(),
            );

            event(
                new TransactionEvent(
                    $transactionDto,
                    $accountDto,
                    $lockedAccount
                )
            );
            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
    /**
     * @inheritDoc
     */
    public function transfer(
        string $senderAccountNumber,
        string $recipientAccountNumber,
        string $senderAccountPin,
        int|float $amount,
        string $description = null
    ): void {
        $minimun_amount  = 300;
        if ($senderAccountNumber == $recipientAccountNumber) {
            throw new Exception('send account number and reciever account number must not be to the same');
        }
        if ($amount < $minimun_amount) {
            throw new AmountToLowException(
                "amount must be equal or greater than $minimun_amount",
                HttpFoundationResponse::HTTP_BAD_REQUEST
            );
        }
        try {
            DB::beginTransaction();
            $senderAccountQuery = $this->modelQuery()->where(
                'account_number',
                $senderAccountNumber
            );
            $recipientAccountQuery = $this->modelQuery()->where(
                'account_number',
                $recipientAccountNumber
            );

            $this->accountExist($senderAccountQuery);
            $this->accountExist($recipientAccountQuery);

            $lockedSenderAccount = $senderAccountQuery->lockForUpdate()->first();
            $lockedRecipientAccount = $recipientAccountQuery->lockForUpdate()->first();

            $senderAccountDto = AccountDto::fromModel($lockedSenderAccount);
            $recipientAccountDto = AccountDto::fromModel($lockedRecipientAccount);

            if (!$this->userService->validatePin(
                $senderAccountDto->getUserId(),
                $senderAccountPin
            )) {
                throw new InvaildPinException();
            }

            $withdrawDto = new WithdrawDto();
            $depositDto = new DepositDto();

            $withdrawDto->setAccountNumber($senderAccountDto->getAccountNumber());
            $withdrawDto->setPin($senderAccountPin);
            $withdrawDto->setAmount($amount);
            $withdrawDto->setDescription($description);

            $this->canWithdraw($senderAccountDto, $withdrawDto);

            $transactionWithdrawDto = TransactionDto::forWithdraw(
                $senderAccountDto,
                $this->transactionService->generateReference(),
                $withdrawDto->getAmount(),
                $withdrawDto->getDescription(),
            );
            $depositDto->setAccount_number($recipientAccountDto->getAccountNumber());
            $depositDto->setAmount($amount);
            $depositDto->setDescription($description);

            $transactionDepositDto = TransactionDto::forDeposit(
                $recipientAccountDto,
                $this->transactionService->generateReference(),
                $depositDto->getAmount(),
                $depositDto->getDescription(),
            );

            $transferDto = new TransferDto();
            $transferDto->setReference($this->transferService->generateReference());

            $transferDto->setSenderAccountId($senderAccountDto->getId());
            $transferDto->setSenderId($senderAccountDto->getUserId());

            $transferDto->setRecipientAccountId($recipientAccountDto->getId());
            $transferDto->setRecipientId($recipientAccountDto->getUserId());

            $transferDto->setAmount($amount);

            $transfer = $this->transferService->createTransfer($transferDto);

            $transactionDepositDto->setTransferId($transfer->id);
            $transactionWithdrawDto->setTransferId($transfer->id);

            event(new TransactionEvent(
                $transactionWithdrawDto,
                $senderAccountDto,
                $lockedSenderAccount
            ));

            event(new TransactionEvent(
                $transactionDepositDto,
                $recipientAccountDto,
                $lockedRecipientAccount
            ));

            DB::commit();
        } catch (Exception $exception) {
            DB::rollBack();
            throw $exception;
        }
    }
}
