<?php

namespace App\Listeners;

use App\Dtos\TransactionDto;
use App\Enums\TransactionCategoryEnum;
use App\Events\DepositEvent;
use App\Services\TransactionService;

class DepositListener
{
    /**
     * Create the event listener.
     */
    public function __construct(private readonly TransactionService $transactionService)
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(DepositEvent $event): void
    {
        if ($event->transactionDto->getCategory() != TransactionCategoryEnum::DEPOSIT->value) {
            return;
        }
        $this->transactionService->createTransaction($event->transactionDto);
        $account = $event->lockedAccount;
        $account->balance += $event->transactionDto->getAmount();
        $account->save();
        $account = $account->refresh();
        $this->transactionService->updateTransactionBalance(
            $event->transactionDto->getReference(),
            $account->balance
        );
    }
}
