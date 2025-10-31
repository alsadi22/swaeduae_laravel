<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Services\StripePaymentService;
use App\Services\InvoiceService;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PaymentController extends Controller
{
    protected $paymentService;
    protected $invoiceService;

    public function __construct(
        StripePaymentService $paymentService,
        InvoiceService $invoiceService
    ) {
        $this->paymentService = $paymentService;
        $this->invoiceService = $invoiceService;
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
            'description' => 'nullable|string',
        ]);

        $result = $this->paymentService->createPaymentIntent(
            Auth::id(),
            $validated['amount'],
            'AED',
            $validated
        );

        if ($result['success']) {
            return response()->json($result, 201);
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
        ]);

        $result = $this->paymentService->handleSuccessfulPayment(
            $validated['intent_id'],
            Auth::id(),
            $validated['payment_type'],
            $validated['amount']
        );

        if ($result['success']) {
            return response()->json($result, 200);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Get payment methods
     */
    public function getMethods()
    {
        $methods = $this->paymentService->getUserPaymentMethods(Auth::id());

        return response()->json($methods);
    }

    /**
     * Store payment method
     */
    public function storeMethod(Request $request)
    {
        $validated = $request->validate([
            'payment_method_id' => 'required|string',
        ]);

        $result = $this->paymentService->storePaymentMethod(
            Auth::id(),
            $validated['payment_method_id']
        );

        if ($result['success']) {
            return response()->json($result, 201);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Delete payment method
     */
    public function deleteMethod($methodId)
    {
        $result = $this->paymentService->deletePaymentMethod($methodId, Auth::id());

        if ($result['success']) {
            return response()->json($result);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Refund payment
     */
    public function refund(Request $request)
    {
        $validated = $request->validate([
            'payment_id' => 'required|numeric',
            'amount' => 'nullable|numeric',
            'reason' => 'nullable|string',
        ]);

        $result = $this->paymentService->refundPayment(
            $validated['payment_id'],
            $validated['amount'] ?? null,
            $validated['reason'] ?? 'customer_request'
        );

        if ($result['success']) {
            return response()->json($result, 200);
        }

        return response()->json(['error' => $result['error']], 400);
    }

    /**
     * Get payment history
     */
    public function history()
    {
        $payments = Payment::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return response()->json($payments);
    }

    /**
     * Get payment by ID
     */
    public function show($paymentId)
    {
        $payment = Payment::where('user_id', Auth::id())
            ->findOrFail($paymentId);

        return response()->json($payment);
    }
}
