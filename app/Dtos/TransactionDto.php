<?php

namespace App\Dtos;

use App\Enums\TransactionCategoryEnum;
use App\Interfaces\DtoInterface;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;

class TransactionDto
{
    private int $id;
    private string $reference;
    private int $userId;
    private int $accountId;
    private int $transferId;
    private float $amount;
    private float $balance;
    private string $category;
    private bool $confirmed;
    private string $description;
    private string $meta;
    private Carbon $date;
    private Carbon $created_at;
    private Carbon $updated_at;

    /**
     * @return int
     */
    public function getId(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return self
     */
    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string
     */
    public function getReference(): string
    {
        return $this->reference;
    }

    /**
     * @param string $reference
     * @return self
     */
    public function setReference(string $reference): self
    {
        $this->reference = $reference;
        return $this;
    }

    /**
     * @return int
     */
    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param int $userId
     * @return self
     */
    public function setUserId(int $userId): self
    {
        $this->userId = $userId;
        return $this;
    }

    /**
     * @return int
     */
    public function getAccountId(): int
    {
        return $this->accountId;
    }

    /**
     * @param int $accountId
     * @return self
     */
    public function setAccountId(int $accountId): self
    {
        $this->accountId = $accountId;
        return $this;
    }

    /**
     * @return int
     */
    public function getTransferId(): int
    {
        return $this->transferId;
    }

    /**
     * @param int $transferId
     * @return self
     */
    public function setTransferId(int $transferId): self
    {
        $this->transferId = $transferId;
        return $this;
    }

    /**
     * @return float
     */
    public function getAmount(): float
    {
        return $this->amount;
    }

    /**
     * @param float $amount
     * @return self
     */
    public function setAmount(float $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return float
     */
    public function getBalance(): float
    {
        return $this->balance;
    }

    /**
     * @param float $balance
     * @return self
     */
    public function setBalance(float $balance): self
    {
        $this->balance = $balance;
        return $this;
    }

    /**
     * @return string
     */
    public function getCategory(): string
    {
        return $this->category;
    }

    /**
     * @param string $category
     * @return self
     */
    public function setCategory(string $category): self
    {
        $this->category = $category;
        return $this;
    }

    /**
     * @return bool
     */
    public function getConfirmed(): bool
    {
        return $this->confirmed;
    }

    /**
     * @param bool $confirmed
     * @return self
     */
    public function setConfirmed(bool $confirmed): self
    {
        $this->confirmed = $confirmed;
        return $this;
    }

    /**
     * @return string
     */
    public function getDescription(): string
    {
        return $this->description;
    }

    /**
     * @param string $description
     * @return self
     */
    public function setDescription(string $description): self
    {
        $this->description = $description;
        return $this;
    }

    /**
     * @return string
     */
    public function getMeta(): string
    {
        return $this->meta;
    }

    /**
     * @param string $meta
     * @return self
     */
    public function setMeta(string $meta): self
    {
        $this->meta = $meta;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getDate(): Carbon
    {
        return $this->date;
    }

    /**
     * @param Carbon $date
     * @return self
     */
    public function setDate(Carbon $date): self
    {
        $this->date = $date;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->created_at;
    }

    /**
     * @param Carbon $created_at
     * @return self
     */
    public function setCreatedAt(Carbon $created_at): self
    {
        $this->created_at = $created_at;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updated_at;
    }

    /**
     * @param Carbon $updated_at
     * @return self
     */
    public function setUpdatedAt(Carbon $updated_at): self
    {
        $this->updated_at = $updated_at;
        return $this;
    }


    public static function forDeposit(
        AccountDto $accountDto,
        string $reference,
        $amount,
        $description
    ): TransactionDto {
        $dto = new TransactionDto();
        $dto->setUserId($accountDto->getUserId())
            ->setAccountId($accountDto->getId())
            ->setReference($reference)
            ->setAmount($amount)
            ->setCategory(TransactionCategoryEnum::DEPOSIT->value)
            ->setDate(Carbon::now())
            ->setDescription($description);
        return $dto;
    }

    public static function forDepositToArray(TransactionDto $transactionDto): array
    {
        return [
            'user_id' => $transactionDto->getUserId(),
            'reference' => $transactionDto->getReference(),
            'account_id' => $transactionDto->getAccountId(),
            'date' => $transactionDto->getDate(),
            'category' => $transactionDto->getCategory(),
            'description' => $transactionDto->getDescription(),
            'amount' => $transactionDto->getAmount(),
        ];
    }
    public static function forWithdraw(
        AccountDto $accountDto,
        string $reference,
        $amount,
        $description
    ): TransactionDto {
        $dto = new TransactionDto();
        $dto->setUserId($accountDto->getUserId())
            ->setAccountId($accountDto->getId())
            ->setReference($reference)
            ->setAmount($amount)
            ->setCategory(TransactionCategoryEnum::WITHDRAW->value)
            ->setDate(Carbon::now())
            ->setDescription($description);
        return $dto;
    }
    public static function forWithdrawToArray(TransactionDto $transactionDto): array
    {
        return [
            'user_id' => $transactionDto->getUserId(),
            'reference' => $transactionDto->getReference(),
            'account_id' => $transactionDto->getAccountId(),
            'date' => $transactionDto->getDate(),
            'category' => $transactionDto->getCategory(),
            'description' => $transactionDto->getDescription(),
            'amount' => $transactionDto->getAmount(),
        ];
    }
}
