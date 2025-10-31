<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use App\Models\User;
use Carbon\Carbon;

class InvoiceService
{
    /**
     * Create invoice
     */
    public function createInvoice($userId, $amount, $description = null, $dueDate = null, $lineItems = [])
    {
        $invoiceNumber = $this->generateInvoiceNumber();

        $invoice = Invoice::create([
            'invoice_number' => $invoiceNumber,
            'user_id' => $userId,
            'subtotal' => $amount,
            'tax_amount' => 0,
            'discount_amount' => 0,
            'total_amount' => $amount,
            'status' => 'draft',
            'issue_date' => now(),
            'due_date' => $dueDate ?? now()->addDays(30),
            'description' => $description,
            'line_items' => $lineItems,
        ]);

        return $invoice;
    }

    /**
     * Send invoice
     */
    public function sendInvoice($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        if ($invoice->status !== 'draft') {
            return ['success' => false, 'error' => 'Only draft invoices can be sent'];
        }

        $invoice->update(['status' => 'sent']);

        // Send email notification
        \Mail::send('emails.invoice-sent', ['invoice' => $invoice], function ($message) use ($invoice) {
            $message->to($invoice->user->email)
                    ->subject("Invoice {$invoice->invoice_number}");
        });

        return ['success' => true, 'invoice' => $invoice];
    }

    /**
     * Mark invoice as paid
     */
    public function markAsPaid($invoiceId, $paymentId = null)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $invoice->update([
            'status' => 'paid',
            'payment_id' => $paymentId,
            'paid_date' => now(),
        ]);

        return $invoice;
    }

    /**
     * Get unpaid invoices
     */
    public function getUnpaidInvoices($userId)
    {
        return Invoice::where('user_id', $userId)
            ->where('status', '!=', 'paid')
            ->where('status', '!=', 'cancelled')
            ->orderBy('due_date')
            ->get();
    }

    /**
     * Get overdue invoices
     */
    public function getOverdueInvoices($userId)
    {
        return Invoice::where('user_id', $userId)
            ->where('status', '!=', 'paid')
            ->where('due_date', '<', now())
            ->get();
    }

    /**
     * Generate invoice number
     */
    private function generateInvoiceNumber(): string
    {
        $year = date('Y');
        $month = date('m');
        $lastInvoice = Invoice::whereYear('created_at', $year)
            ->whereMonth('created_at', $month)
            ->orderBy('id', 'desc')
            ->first();

        $number = ($lastInvoice ? intval(substr($lastInvoice->invoice_number, -4)) + 1 : 1);

        return "INV-{$year}{$month}-" . str_pad($number, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Get invoice PDF
     */
    public function generateInvoicePdf($invoiceId)
    {
        $invoice = Invoice::findOrFail($invoiceId);

        $pdf = \PDF::loadView('invoices.pdf', ['invoice' => $invoice]);

        return $pdf;
    }

    /**
     * Download invoice
     */
    public function downloadInvoice($invoiceId)
    {
        $pdf = $this->generateInvoicePdf($invoiceId);

        return $pdf->download("invoice-{$invoiceId}.pdf");
    }
}
