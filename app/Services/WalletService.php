<?php

namespace App\Services;

use App\Models\Wallet;
use App\Models\WalletTransaction;

class WalletService
{
    /**
     * Get or create wallet for user
     */
    public function getOrCreateWallet($userId)
    {
        return Wallet::firstOrCreate(
            ['user_id' => $userId],
            [
                'balance' => 0,
                'total_spent' => 0,
                'total_received' => 0,
                'currency' => 'AED',
            ]
        );
    }

    /**
     * Add balance to wallet
     */
    public function addBalance($userId, $amount, $reason, $description = null, $metadata = [])
    {
        $wallet = $this->getOrCreateWallet($userId);

        $wallet->addBalance($amount);

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => 'credit',
            'reason' => $reason,
            'description' => $description,
            'metadata' => $metadata,
        ]);

        return ['success' => true, 'wallet' => $wallet];
    }

    /**
     * Deduct balance from wallet
     */
    public function deductBalance($userId, $amount, $reason, $description = null, $metadata = [])
    {
        $wallet = $this->getOrCreateWallet($userId);

        if (!$wallet->deductBalance($amount)) {
            return ['success' => false, 'error' => 'Insufficient balance'];
        }

        WalletTransaction::create([
            'wallet_id' => $wallet->id,
            'amount' => $amount,
            'type' => 'debit',
            'reason' => $reason,
            'description' => $description,
            'metadata' => $metadata,
        ]);

        return ['success' => true, 'wallet' => $wallet];
    }

    /**
     * Get wallet balance
     */
    public function getBalance($userId)
    {
        $wallet = $this->getOrCreateWallet($userId);
        return $wallet->balance;
    }

    /**
     * Check if user has sufficient balance
     */
    public function hasSufficientBalance($userId, $amount): bool
    {
        $wallet = $this->getOrCreateWallet($userId);
        return $wallet->hassufficientBalance($amount);
    }

    /**
     * Get wallet transactions
     */
    public function getTransactions($userId, $limit = 20)
    {
        $wallet = $this->getOrCreateWallet($userId);

        return WalletTransaction::where('wallet_id', $wallet->id)
            ->recent()
            ->limit($limit)
            ->get();
    }

    /**
     * Get wallet summary
     */
    public function getSummary($userId)
    {
        $wallet = $this->getOrCreateWallet($userId);

        return [
            'balance' => $wallet->balance,
            'total_received' => $wallet->total_received,
            'total_spent' => $wallet->total_spent,
            'currency' => $wallet->currency,
        ];
    }
}
