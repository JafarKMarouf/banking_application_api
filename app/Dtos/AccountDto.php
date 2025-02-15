<?php

namespace App\Dtos;

use App\Interfaces\DtoInterface;
use App\Models\Account;
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


    public static function fromModel(Model|Account $model): AccountDto
    {
        $accountDto = new AccountDto();
        $accountDto->setId($model->id);
        $accountDto->setUserId($model->user_id);
        $accountDto->setAccountNumber($model->account_number);
        $accountDto->setBalance($model->balance);
        return $accountDto;
    }


    /**
     * Get the value of id
     */
    public function getId(): int
    {
        return $this->id;
    }

    public function setId(int $id): static
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

    public function setUserId(int $userId): static
    {
        $this->userId = $userId;

        return $this;
    }

    /**
     * Get the value of accountNumber
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    public function setAccountNumber($accountNumber): static
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * Get the value of balance
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    public function setBalance(float|int $balance): static
    {
        $this->balance = $balance;

        return $this;
    }

    /**
     * Get the value of created_at
     */
    public function getCreated_at(): Carbon
    {
        return $this->created_at;
    }

    public function setCreated_at($created_at): static
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Get the value of updated_at
     */
    public function getUpdated_at(): Carbon
    {
        return $this->updated_at;
    }

    public function setUpdated_at($updated_at): static
    {
        $this->updated_at = $updated_at;

        return $this;
    }


    /**
     * @param Model $model
     * @return array
     */
    public static function toArray(Model $model): array
    {
        // TODO: Implement toArray() method.
    }

    /**
     * @param FormRequest $request
     * @return self
     */
    public static function fromApiFormRequest(FormRequest $request): DtoInterface
    {
        // TODO: Implement fromApiFormRequest() method.
    }
}
