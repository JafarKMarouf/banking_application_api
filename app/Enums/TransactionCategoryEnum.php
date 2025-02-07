<?php

namespace App\Enums;

enum TransactionCategoryEnum: string
{
    case WITHDRAW = "withdraw";
    case DEPOSIT = "deposit";
}
