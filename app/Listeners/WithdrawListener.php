<?php

namespace App\Listeners;

use App\Enums\TransactionCategoryEnum;
use App\Events\TransactionEvent;
use App\Services\TransactionService;

class WithdrawListener
{
    /**
     * Create the event listener.
     */
    public function __construct(
        private readonly TransactionService $transactionService

    ) {}

    /**
     * Handle the event.
     */
    public function handle(TransactionEvent $event): void
    {
        if ($event->transactionDto->getCategory() != TransactionCategoryEnum::WITHDRAW->value) {
            return;
        }
        $transaction = $this->transactionService->createTransaction($event->transactionDto);
        $account = $event->lockedAccount;
        $account->balance -= $event->transactionDto->getAmount();
        $account->save();
        $account = $account->refresh();
        $this->transactionService->updateTransactionBalance(
            $event->transactionDto->getReference(),
            $account->balance
        );
        if ($transaction->getTransferId) {
            $this->transactionService->updateTransferId(
                $event->transactionDto->getReference(),
                $event->transactionDto->getTransferId()
            );
        }
    }
}
