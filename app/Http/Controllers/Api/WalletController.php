<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\WalletService;
use Illuminate\Support\Facades\Auth;

class WalletController extends Controller
{
    protected $walletService;

    public function __construct(WalletService $walletService)
    {
        $this->walletService = $walletService;
    }

    /**
     * Get wallet summary
     */
    public function summary()
    {
        $wallet = $this->walletService->getSummary(Auth::id());

        return response()->json($wallet);
    }

    /**
     * Get wallet balance
     */
    public function balance()
    {
        $balance = $this->walletService->getBalance(Auth::id());

        return response()->json(['balance' => $balance]);
    }

    /**
     * Get transactions
     */
    public function transactions()
    {
        $limit = request()->get('limit', 20);
        $transactions = $this->walletService->getTransactions(Auth::id(), $limit);

        return response()->json($transactions);
    }

    /**
     * Add balance
     */
    public function addBalance(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'reason' => 'nullable|string',
        ]);

        $result = $this->walletService->addBalance(
            Auth::id(),
            $validated['amount'],
            $validated['reason'] ?? 'manual_topup',
            'Wallet top-up via API'
        );

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json(['error' => 'Failed to add balance'], 400);
    }

    /**
     * Deduct balance
     */
    public function deductBalance(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
            'reason' => 'required|string',
            'description' => 'nullable|string',
        ]);

        $result = $this->walletService->deductBalance(
            Auth::id(),
            $validated['amount'],
            $validated['reason'],
            $validated['description'] ?? null
        );

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Check balance
     */
    public function checkBalance(\Illuminate\Http\Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $hasSufficient = $this->walletService->hasSufficientBalance(
            Auth::id(),
            $validated['amount']
        );

        return response()->json(['has_sufficient_balance' => $hasSufficient]);
    }
}
