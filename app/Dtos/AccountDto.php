<?php

namespace App\Dtos;

use App\Interfaces\DtoInterface;
use App\Models\Account;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class AccountDto implements DtoInterface
{

    private int $id;
    public int $userId;
    private string $accountNumber;
    private float $balance;
    private Carbon $created_at;
    private Carbon $updated_at;
    /**
     * @inheritDoc
     */
    public static function fromApiFormRequest(FormRequest $request): DtoInterface
    {
        $accountDto = new AccountDto();
        return $accountDto;
    }

    /**
     * @inheritDoc
     */
    public static function fromModel(Model $account): DtoInterface
    {
        $accountDto = new AccountDto();
        $accountDto->setId($account->id);
        $accountDto->setUserId($account->user_id);
        $accountDto->setAccountNumber($account->account_number);
        $accountDto->setBalance($account->balance);
        return $accountDto;
    }

    /**
     * @inheritDoc
     */
    public static function toArray(Model $model): array
    {
        return [];
    }

    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * Set the value of id
     *
     * @return  self
     */
    public function setId($id): static
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Get the value of userId
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * Set the value of userId
     *
     * @return  self
     */
    public function setUserId($userId)
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get the value of accountNumber
     */
    public function getAccountNumber()
    {
        return $this->accountNumber;
    }

    /**
     * Set the value of accountNumber
     *
     * @return  self
     */
    public function setAccountNumber($accountNumber)
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get the value of balance
     */
    public function getBalance()
    {
        return $this->balance;
    }

    /**
     * Set the value of balance
     *
     * @return  self
     */
    public function setBalance($balance)
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get the value of created_at
     */
    public function getCreated_at()
    {
        return $this->created_at;
    }

    /**
     * Set the value of created_at
     *
     * @return  self
     */
    public function setCreated_at($created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of updated_at
     */
    public function getUpdated_at()
    {
        return $this->updated_at;
    }

    /**
     * Set the value of updated_at
     *
     * @return  self
     */
    public function setUpdated_at($updated_at)
    {
        $this->updated_at = $updated_at;

        return $this;
    }
}
