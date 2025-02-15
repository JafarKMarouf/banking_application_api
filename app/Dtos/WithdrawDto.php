<?php

namespace App\Dtos;

use App\Enums\TransactionCategoryEnum;

class WithdrawDto
{
    private string $accountNumber;
    private string $pin;
    private float $amount;
    private string $category;
    private string $description;


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
        $this->category = TransactionCategoryEnum::WITHDRAW->value;

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
     *
     * @param $description
     * @return  self
     */
    public function setDescription($description):static
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
     * @param $amount
     * @return  self
     */
    public function setAmount($amount):static
    {
        $this->amount = $amount;

        return $this;
    }

    /**
     * Get the value of accountNumber
     */
    public function getAccountNumber(): string
    {
        return $this->accountNumber;
    }

    /**
     * Set the value of accountNumber
     * @param $accountNumber
     * @return  self
     */
    public function setAccountNumber($accountNumber):static
    {
        $this->accountNumber = $accountNumber;

        return $this;
    }

    /**
     * @return string
     */
    public function getPin(): string
    {
        return $this->pin;
    }

    /**
     * @param string $pin
     * @return self
     */
    public function setPin(string $pin): self
    {
        $this->pin = $pin;
        return $this;
    }
}
