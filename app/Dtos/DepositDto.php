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
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Set the value of category
     *
     * @return  self
     */
    public function setCategory()
    {
        $this->category = TransactionCategoryEnum::DEPOSIT->value;

        return $this;
    }

    /**
     * Get the value of description
     */
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * Set the value of description
     *
     * @return  self
     */
    public function setDescription($description)
    {
        $this->description = $description;

        return $this;
    }

    /**
     * Get the value of amount
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * Set the value of amount
     *
     * @return  self
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of account_number
     */
    public function getAccount_number()
    {
        return $this->account_number;
    }

    /**
     * Set the value of account_number
     *
     * @return  self
     */
    public function setAccount_number($account_number)
    {
        $this->account_number = $account_number;

        return $this;
    }
    /**
     * @inheritDoc
     */
    public static function fromApiFormRequest(FormRequest $request): DtoInterface
    {
        $depositDto = new DepositDto();
        $depositDto->setAccount_number($request->input('account_number'));
        $depositDto->setAmount($request->input('amount'));
        $depositDto->setDescription($request->input('description'));
        return $depositDto;
    }

    /**
     * @inheritDoc
     */
    public static function fromModel(Model $model): DtoInterface
    {
        $dto = new DepositDto();
        return $dto;
    }

    /**
     * @inheritDoc
     */
    public static function toArray(Model $model): array
    {
        return [];
    }
}
