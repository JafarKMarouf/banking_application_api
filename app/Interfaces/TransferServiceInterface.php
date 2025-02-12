<?php

namespace App\Interfaces;

use App\Dtos\TransferDto;
use App\Models\Transfer;
use Illuminate\Contracts\Database\Eloquent\Builder;

interface TransferServiceInterface
{
    public function modelQuery(): Builder;

    public function generateReference(): string;

    public function createTransfer(TransferDto $transferDto): Transfer;
}
