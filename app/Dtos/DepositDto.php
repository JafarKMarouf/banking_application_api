<?php

namespace App\Dtos;

use App\Enums\TransactionCategoryEnum;
use App\Interfaces\DtoInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class DepositDto implements DtoInterface
{
    private string $account_number;
    private float $amount;
    private string $description;
    private string $category;

    /**
     * Get the value of category
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @return  self
     */
    public function setCategory(): static
    {
        $this->category = TransactionCategoryEnum::DEPOSIT->value;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * Set the value of description
     * @param string $description
     * @return  self
     */
    public function setDescription(string $description): static
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of amount
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     * @param int|float $amount
     * @return  self
     */
    public function setAmount(int|float $amount): static
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of account_number
     */
    public function getAccount_number(): string
    {
        return $this->account_number;
    }

    /**
     * Set the value of account_number
     * @param string $account_number
     * @return  self
     */
    public function setAccount_number(string $account_number): static
    {
        $this->account_number = $account_number;

        return $this;
    }
    public static function fromApiFormRequest(FormRequest $request): DtoInterface
    {
        $depositDto = new DepositDto();
        $depositDto->setAccount_number($request->input('account_number'));
        $depositDto->setAmount($request->input('amount'));
        $depositDto->setDescription($request->input('description'));
        return $depositDto;
    }

    public static function fromModel(Model $model): DtoInterface
    {
        return new DepositDto();
    }

    public static function toArray(Model $model): array
    {
        return [];
    }
}
