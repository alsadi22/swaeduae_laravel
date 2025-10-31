<?php

namespace App\Services;

use App\Models\Payment;
use App\Models\PaymentMethod;
use App\Models\Refund;
use App\Models\Transaction;
use App\Models\Invoice;
use Stripe\Stripe;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod as StripePaymentMethod;
use Stripe\Customer;

class StripePaymentService
{
    protected $stripeKey;
    protected $stripeSecret;

    public function __construct()
    {
        $this->stripeSecret = config('services.stripe.secret');
        // Stripe is lazily initialized on first use
        if ($this->stripeSecret) {
            try {
                Stripe::setApiKey($this->stripeSecret);
            } catch (\Exception $e) {
                // Stripe not installed, will fail gracefully
            }
        }
    }

    /**
     * Create a payment intent
     */
    public function createPaymentIntent($userId, $amount, $currency = 'aed', $metadata = [])
    {
        try {
            $intent = PaymentIntent::create([
                'amount' => $amount * 100, // Convert to cents/fils
                'currency' => strtolower($currency),
                'metadata' => array_merge($metadata, ['user_id' => $userId]),
            ]);

            return [
                'success' => true,
                'client_secret' => $intent->client_secret,
                'intent_id' => $intent->id,
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Retrieve payment intent
     */
    public function getPaymentIntent($intentId)
    {
        try {
            return PaymentIntent::retrieve($intentId);
        } catch (\Exception $e) {
            return null;
        }
    }

    /**
     * Handle successful payment
     */
    public function handleSuccessfulPayment($intentId, $userId, $paymentType, $amount, $referenceId = null, $referenceType = null)
    {
        try {
            $intent = $this->getPaymentIntent($intentId);

            if (!$intent || $intent->status !== 'succeeded') {
                return ['success' => false, 'error' => 'Payment not confirmed'];
            }

            // Create payment record
            $payment = Payment::create([
                'user_id' => $userId,
                'payment_method' => $this->getPaymentMethod($intent),
                'amount' => $amount,
                'currency' => strtoupper($intent->currency),
                'transaction_id' => $intent->id,
                'status' => 'completed',
                'payment_type' => $paymentType,
                'reference_id' => $referenceId,
                'reference_type' => $referenceType,
                'receipt_number' => 'RCP-' . time(),
                'completed_at' => now(),
            ]);

            // Create transaction record
            Transaction::create([
                'user_id' => $userId,
                'transaction_type' => 'payment',
                'amount' => $amount,
                'currency' => strtoupper($intent->currency),
                'status' => 'completed',
                'reference_id' => $payment->id,
                'reference_type' => 'Payment',
                'completed_at' => now(),
            ]);

            return ['success' => true, 'payment' => $payment];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Store payment method
     */
    public function storePaymentMethod($userId, $paymentMethodId)
    {
        try {
            $stripeMethod = StripePaymentMethod::retrieve($paymentMethodId);

            $existingDefault = PaymentMethod::where('user_id', $userId)
                ->where('is_default', true)
                ->first();

            $paymentMethod = PaymentMethod::create([
                'user_id' => $userId,
                'stripe_payment_method_id' => $paymentMethodId,
                'type' => $stripeMethod->type,
                'card_brand' => $stripeMethod->card->brand ?? null,
                'card_last_four' => $stripeMethod->card->last4 ?? null,
                'card_expiry' => ($stripeMethod->card->exp_month ?? '') . '/' . ($stripeMethod->card->exp_year ?? ''),
                'card_holder_name' => $stripeMethod->billing_details->name ?? null,
                'is_default' => !$existingDefault,
                'added_at' => now(),
            ]);

            return ['success' => true, 'method' => $paymentMethod];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Refund payment
     */
    public function refundPayment($paymentId, $amount = null, $reason = 'customer_request')
    {
        try {
            $payment = Payment::findOrFail($paymentId);

            if (!$payment->canBeRefunded()) {
                return ['success' => false, 'error' => 'Payment cannot be refunded'];
            }

            $refundAmount = $amount ?? $payment->amount;

            $refund = \Stripe\Refund::create([
                'payment_intent' => $payment->transaction_id,
                'amount' => $refundAmount * 100,
            ]);

            // Create refund record
            $refundRecord = Refund::create([
                'payment_id' => $paymentId,
                'user_id' => $payment->user_id,
                'amount' => $refundAmount,
                'currency' => $payment->currency,
                'stripe_refund_id' => $refund->id,
                'status' => 'completed',
                'reason' => $reason,
                'completed_at' => now(),
            ]);

            // Update payment status if fully refunded
            if ($refundAmount === $payment->amount) {
                $payment->update(['status' => 'refunded']);
            }

            return ['success' => true, 'refund' => $refundRecord];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Get payment method from intent
     */
    private function getPaymentMethod($intent)
    {
        if ($intent->payment_method_types) {
            return $intent->payment_method_types[0] ?? 'card';
        }

        return 'card';
    }

    /**
     * Get user's payment methods
     */
    public function getUserPaymentMethods($userId)
    {
        return PaymentMethod::where('user_id', $userId)
            ->where('is_active', true)
            ->orderBy('is_default', 'desc')
            ->get();
    }

    /**
     * Delete payment method
     */
    public function deletePaymentMethod($paymentMethodId, $userId)
    {
        try {
            $method = PaymentMethod::where('id', $paymentMethodId)
                ->where('user_id', $userId)
                ->first();

            if (!$method) {
                return ['success' => false, 'error' => 'Method not found'];
            }

            $method->update(['is_active' => false]);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }

    /**
     * Set default payment method
     */
    public function setDefaultPaymentMethod($paymentMethodId, $userId)
    {
        try {
            // Remove default from others
            PaymentMethod::where('user_id', $userId)
                ->update(['is_default' => false]);

            // Set new default
            PaymentMethod::where('id', $paymentMethodId)
                ->where('user_id', $userId)
                ->update(['is_default' => true]);

            return ['success' => true];
        } catch (\Exception $e) {
            return ['success' => false, 'error' => $e->getMessage()];
        }
    }
}
