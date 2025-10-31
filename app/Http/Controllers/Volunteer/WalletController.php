<?php

namespace App\Http\Controllers\Volunteer;

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
     * Show wallet
     */
    public function index()
    {
        $wallet = $this->walletService->getSummary(Auth::id());
        $transactions = $this->walletService->getTransactions(Auth::id(), 50);

        return view('volunteer.wallet.index', compact('wallet', 'transactions'));
    }

    /**
     * Get wallet balance
     */
    public function getBalance()
    {
        $balance = $this->walletService->getBalance(Auth::id());

        return response()->json(['balance' => $balance]);
    }

    /**
     * Get transaction history
     */
    public function transactions()
    {
        $transactions = $this->walletService->getTransactions(Auth::id(), 100);

        return view('volunteer.wallet.transactions', compact('transactions'));
    }

    /**
     * Add balance to wallet
     */
    public function addBalance(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $result = $this->walletService->addBalance(
            Auth::id(),
            $validated['amount'],
            'manual_topup',
            'User wallet top-up'
        );

        if ($result['success']) {
            return back()->with('success', 'Balance added successfully');
        }

        return back()->with('error', 'Failed to add balance');
    }
}
