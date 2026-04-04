<?php

namespace App\Http\Controllers\Kiosk;

use App\Http\Controllers\Controller;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DestroyTransactionPaymentController extends Controller
{
    public function destroyWithTransaction(Request $request)
    {
        $transactionId = $request->session()->get('transaction_id');

        if ($transactionId) {
            $payment = Payment::query()
                ->where('transaction_id', $transactionId)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->first();

            $payment?->delete();
        }

        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        $request->session()->flash('success', 'You have been safely logged out');
        return to_route('login');
    }

    public function removeTransaction(Request $request)
    {
        $transactionId = $request->session()->get('transaction_id');

        if ($transactionId) {
            $payment = Payment::query()
                ->where('transaction_id', $transactionId)
                ->where('user_id', auth()->id())
                ->where('status', 'pending')
                ->first();

            $payment?->delete();
            $request->session()->forget('transaction_id');
        }

        return redirect()->route('kiosk.outstanding-balance');
    }
}
