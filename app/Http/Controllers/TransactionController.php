<?php

namespace App\Http\Controllers;

use App\Http\Requests\FilterTransactionRequest;
use App\Http\Response\Response;
use App\Services\TransactionService;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function __construct(private readonly TransactionService $transactionService) {}

    public function index(FilterTransactionRequest $request)
    {
        $user = $request->user();
        $transactionBuilder = $this->transactionService
            ->modelQuery()
            ->when(
                request()->query('category'),
                function ($query, $category) {
                    $query->where('category', $category);
                }
            )->when(
                request()->query('start_date'),
                function ($query, $start_date) {
                    $end_date = request()->query('end_date');
                    $query->whereDate(
                        'date',
                        '>=',
                        $start_date
                    )->whereDate(
                        'date',
                        '<=',
                        $end_date
                    );
                }
            );
        $transactionBuilder = $this->transactionService->getTransactionByUserId(
            $user->id,
            $transactionBuilder
        );
        return Response::success(
            [
                'transaction' => $transactionBuilder->paginate()
            ],
            'Transaction History retreived'
        );
    }
}
