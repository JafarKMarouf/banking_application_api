<?php

namespace App\Services;

use App\Dtos\TransferDto;
use App\Interfaces\TransferServiceInterface;
use App\Models\Transfer;
use Illuminate\Database\Eloquent\Builder;
use \Illuminate\Support\Str;
use \Carbon\Carbon;

class TransferService implements TransferServiceInterface
{

    /**
     * @inheritDoc
     */
    public function modelQuery(): Builder
    {
        return Transfer::query();
    }
    /**
     * @inheritDoc
     */
    public function generateReference(): string
    {
        return Str::upper(
            'TRF' . '/' . Carbon::now()->getTimestampMs() . '/' . Str::random(4)
        );
    }

    public function createTransfer(TransferDto $transferDto): Transfer
    {
        $data =  $transferDto->toArray($transferDto);
        return $this->modelQuery()->create($data);
    }
}
