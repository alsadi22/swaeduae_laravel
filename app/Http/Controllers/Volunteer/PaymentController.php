<?php

namespace App\Http\Controllers\Volunteer;

use App\Http\Controllers\Controller;
use App\Services\StripePaymentService;
use App\Services\WalletService;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $walletService;

    public function __construct(
        StripePaymentService $paymentService,
        WalletService $walletService
    ) {
        $this->paymentService = $paymentService;
        $this->walletService = $walletService;
    }

    /**
     * Show payment page
     */
    public function index()
    {
        $paymentMethods = $this->paymentService->getUserPaymentMethods(Auth::id());
        $wallet = $this->walletService->getSummary(Auth::id());

        return view('volunteer.payments.index', compact('paymentMethods', 'wallet'));
    }

    /**
     * Create payment intent
     */
    public function createIntent(Request $request)
    {
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_type' => 'required|string',
            'reference_id' => 'nullable|numeric',
            'reference_type' => 'nullable|string',
        ]);

        $result = $this->paymentService->createPaymentIntent(
            Auth::id(),
            $validated['amount'],
            'AED',
            [
                'payment_type' => $validated['payment_type'],
                'reference_id' => $validated['reference_id'] ?? null,
            ]
        );

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Confirm payment
     */
    public function confirmPayment(Request $request)
    {
        $validated = $request->validate([
            'intent_id' => 'required|string',
            'payment_type' => 'required|string',
            'amount' => 'required|numeric',
            'reference_id' => 'nullable|numeric',
            'reference_type' => 'nullable|string',
        ]);

        $result = $this->paymentService->handleSuccessfulPayment(
            $validated['intent_id'],
            Auth::id(),
            $validated['payment_type'],
            $validated['amount'],
            $validated['reference_id'] ?? null,
            $validated['reference_type'] ?? null
        );

        if ($result['success']) {
            return back()->with('success', 'Payment successful!');
        }

        return back()->with('error', $result['error'] ?? 'Payment failed');
    }

    /**
     * Get payment methods
     */
    public function getPaymentMethods()
    {
        $methods = $this->paymentService->getUserPaymentMethods(Auth::id());

        return view('volunteer.payments.methods', compact('methods'));
    }

    /**
     * Delete payment method
     */
    public function deleteMethod(Request $request)
    {
        $validated = $request->validate([
            'method_id' => 'required|numeric',
        ]);

        $result = $this->paymentService->deletePaymentMethod(
            $validated['method_id'],
            Auth::id()
        );

        if ($result['success']) {
            return back()->with('success', 'Payment method deleted');
        }

        return back()->with('error', $result['error'] ?? 'Failed to delete');
    }

    /**
     * Set default payment method
     */
    public function setDefault(Request $request)
    {
        $validated = $request->validate([
            'method_id' => 'required|numeric',
        ]);

        $result = $this->paymentService->setDefaultPaymentMethod(
            $validated['method_id'],
            Auth::id()
        );

        if ($result['success']) {
            return back()->with('success', 'Default payment method updated');
        }

        return back()->with('error', $result['error'] ?? 'Failed to update');
    }

    /**
     * View payment history
     */
    public function history()
    {
        $payments = Payment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('volunteer.payments.history', compact('payments'));
    }

    /**
     * View payment details
     */
    public function show(Payment $payment)
    {
        if ($payment->user_id !== Auth::id()) {
            abort(403);
        }

        return view('volunteer.payments.show', compact('payment'));
    }
}
