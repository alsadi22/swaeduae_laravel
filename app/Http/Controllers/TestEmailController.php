<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Mail\TestEmail;

class TestEmailController extends Controller
{
    public function sendTestEmail(Request $request)
    {
        $email = $request->get('email', 'admin@swaeduae.ae');
        $message = $request->get('message', 'This is a test email from SWAED platform.');

        try {
            Mail::to($email)->send(new TestEmail($message));
            return response()->json(['status' => 'success', 'message' => 'Test email sent successfully']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => $e->getMessage()], 500);
        }
    }
}