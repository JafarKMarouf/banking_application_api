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
use App\Exceptions\InvalidAccountNumberException;
use App\Exceptions\InvalidPinException;
use App\Exceptions\NotEnoughBalanceException;
use App\Exceptions\NotSetupPin;
use App\Interfaces\AccountServiceInterface;
use App\Models\Account;
use App\Models\Transfer;
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

    public function modelQuery(): Builder
    {
        return Account::query();
    }

    /**
     *
     * @throws AccountNumberExistsException
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

        /** @var Account $account */
        $account =  $this->modelQuery()->create([
            'account_number' => $mixedAccountNumber,
            'user_id' => $userDto->getId(),
        ]);
        return $account;
    }

    public function getAccountByAccountNumber(string $accountNumber): Account
    {
        /** @var Account $account */
        $account = $this->modelQuery()
            ->where('account_number', $accountNumber)
            ->first();
        return $account;
    }

    /**
     * @throws InvalidAccountNumberException
     */
    public function getAccountByUserId(int $userId): Account
    {
        $accountExist = $this->modelQuery()
            ->where('user_id', $userId)
            ->exists();
        if(!$accountExist){
            throw new InvalidAccountNumberException();
        }
        /** @var Account $account */
        $account = $this->modelQuery()
            ->where('user_id', $userId)
            ->first();
        return $account;
    }

    public function hasAccountNumber(UserDto $userDto): bool
    {
        return $this->modelQuery()
            ->where('user_id', $userDto->getId())
            ->exists();
    }

    /**
     * @param Builder $accountQuery
     * @return void
     * @throws InvalidAccountNumberException
     */
    public function accountExist(Builder $accountQuery): void
    {
        if (!$accountQuery->exists()) {
            throw new InvalidAccountNumberException();
        }
    }

    /**
     * @throws NotEnoughBalanceException
     */
    public function canWithdraw(AccountDto $accountDto, WithdrawDto $withdrawDto): bool
    {
        if ($accountDto->getBalance() < $withdrawDto->getAmount()) {
            throw new NotEnoughBalanceException();
        }

        return true;
    }

    /**
     * @throws InvalidAccountNumberException
     */
    public function validAccountNumber(string $account_number): void
    {
        $user_id = auth()->id();
        $account = $this->getAccountByUserId($user_id);

        if ($account->account_number != $account_number) {
            throw new InvalidAccountNumberException();
        }
    }

    /**
     * @throws InvalidAccountNumberException
     * @throws AmountToLowException
     */
    public function deposit(DepositDto $depositDto): void
    {
        $minimum_amount  = 500;
        if ($depositDto->getAmount() < $minimum_amount) {
            throw new AmountToLowException(
                "amount must be equal or greater than $minimum_amount",
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

            $this->validAccountNumber($depositDto->getAccount_number());

            $user_id = auth()->id();
            $account = $this->getAccountByUserId($user_id);
            if (!empty($account->account_number)) {
                if ($account->account_number != $depositDto->getAccount_number()) {
                    throw new InvalidAccountNumberException();
                }
            }
            $lockedAccount = $accountQuery->lockForUpdate()->first();
            $accountDto = AccountDto::fromModel($lockedAccount);
            $transactionDto = TransactionDto::forDeposit(
                $accountDto,
                $this->transactionService->generateReference(),
                $depositDto->getAmount(),
                $depositDto->getDescription(),
            );
            /** @var Account $lockedAccount */
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
     * @throws NotSetupPin
     * @throws AmountToLowException
     * @throws InvalidPinException
     * @throws InvalidAccountNumberException
     * @throws NotEnoughBalanceException
     */
    public function withdraw(WithdrawDto $withdrawDto): void
    {
        $minimum_amount  = 500;
        if ($withdrawDto->getAmount() < $minimum_amount) {
            throw new AmountToLowException(
                "amount must be equal or greater than $minimum_amount",
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
                throw new InvalidPinException();
            }
            $this->canWithdraw($accountDto, $withdrawDto);
            $transactionDto = TransactionDto::forWithdraw(
                $accountDto,
                $this->transactionService->generateReference(),
                $withdrawDto->getAmount(),
                $withdrawDto->getDescription(),
            );

            /** @var Account $lockedAccount */
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
     * @throws NotSetupPin
     * @throws AmountToLowException
     * @throws InvalidPinException
     * @throws InvalidAccountNumberException
     * @throws NotEnoughBalanceException
     * @throws Exception
     */
    public function transfer(
        string $senderAccountNumber,
        string $recipientAccountNumber,
        string $senderAccountPin,
        int|float $amount,
        string $description = null
    ): void {
        $minimum_amount  = 300;
        if ($senderAccountNumber == $recipientAccountNumber) {
            throw new Exception('send account number and receiver account number must not be to the same');
        }
        if ($amount < $minimum_amount) {
            throw new AmountToLowException(
                "amount must be equal or greater than $minimum_amount",
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
                throw new InvalidPinException();
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

            $transferDto->setSenderAccountId($senderAccountDto->getId());
            $transferDto->setSenderId($senderAccountDto->getUserId());

            $transferDto->setRecipientAccountId($recipientAccountDto->getId());
            $transferDto->setRecipientId($recipientAccountDto->getUserId());

            $transferDto->setReference($this->transferService->generateReference());
            $transferDto->setAmount($amount);

            $transfer = $this->transferService->createTransfer($transferDto);
            $transactionDepositDto->setTransferId($transfer['id']);
            $transactionWithdrawDto->setTransferId($transfer['id']);

            /** @var Account $lockedSenderAccount */
            event(new TransactionEvent(
                $transactionWithdrawDto,
                $senderAccountDto,
                $lockedSenderAccount
            ));

            /** @var Account $lockedRecipientAccount */
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
