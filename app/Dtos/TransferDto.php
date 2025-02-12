<?php

namespace App\Dtos;

use Carbon\Carbon;

class TransferDto
{
    private int $id;

    private int $senderId;

    private int $senderAccountId;

    private int $recipientId;

    private int $recipientAccountId;

    private string $reference;

    private float|int $amount;

    private string $status;

    private Carbon $createdAt;

    private Carbon $updatedAt;


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
     * @return int
     */
    public function getSenderId(): int
    {
        return $this->senderId;
    }

    /**
     * @param int $senderId
     * @return self
     */
    public function setSenderId(int $senderId): self
    {
        $this->senderId = $senderId;
        return $this;
    }

    /**
     * @return int
     */
    public function getSenderAccountId(): int
    {
        return $this->senderAccountId;
    }

    /**
     * @param int $senderAccountId
     * @return self
     */
    public function setSenderAccountId(int $senderAccountId): self
    {
        $this->senderAccountId = $senderAccountId;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecipientId(): int
    {
        return $this->recipientId;
    }

    /**
     * @param int $recipientId
     * @return self
     */
    public function setRecipientId(int $recipientId): self
    {
        $this->recipientId = $recipientId;
        return $this;
    }

    /**
     * @return int
     */
    public function getRecipientAccountId(): int
    {
        return $this->recipientAccountId;
    }

    /**
     * @param int $recipientAccountId
     * @return self
     */
    public function setRecipientAccountId(int $recipientAccountId): self
    {
        $this->recipientAccountId = $recipientAccountId;
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
     * @return float|int
     */
    public function getAmount(): float|int
    {
        return $this->amount;
    }

    /**
     * @param float|int $amount
     * @return self
     */
    public function setAmount(float|int $amount): self
    {
        $this->amount = $amount;
        return $this;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     * @return self
     */
    public function setStatus(string $status): self
    {
        $this->status = $status;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getCreatedAt(): Carbon
    {
        return $this->createdAt;
    }

    /**
     * @param Carbon $createdAt
     * @return self
     */
    public function setCreatedAt(Carbon $createdAt): self
    {
        $this->createdAt = $createdAt;
        return $this;
    }

    /**
     * @return Carbon
     */
    public function getUpdatedAt(): Carbon
    {
        return $this->updatedAt;
    }

    /**
     * @param Carbon $updatedAt
     * @return self
     */
    public function setUpdatedAt(Carbon $updatedAt): self
    {
        $this->updatedAt = $updatedAt;
        return $this;
    }
    public static function toArray(TransferDto $transferDto): array
    {
        return [
            'sender_id' => $transferDto->getSenderId(),
            'reference' => $transferDto->getReference(),
            'sender_account_id' => $transferDto->getSenderAccountId(),
            'recipient_id' => $transferDto->getRecipientId(),
            'recipient_account_id' => $transferDto->getRecipientAccountId(),
            'amount' => $transferDto->getAmount(),
            'status' => 'Pendding',
        ];
    }
}
