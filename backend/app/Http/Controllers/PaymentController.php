<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Payment;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    public function store(Request $request, Order $order)
    {
        $data = $request->validate([
            'method' => ['required', 'in:idram,telcell,visa,mastercard,cash'],
            'amount' => ['required', 'numeric', 'min:0'],
            'transaction_id' => ['nullable', 'string'],
            'status' => ['required', 'in:success,failed,pending'],
        ]);

        $payment = Payment::create([
            'order_id' => $order->id,
            'method' => $data['method'],
            'amount' => $data['amount'],
            'transaction_id' => $data['transaction_id'] ?? null,
            'status' => $data['status'],
            'paid_at' => $data['status'] === 'success' ? now() : null,
        ]);

        // Update order payment status
        if ($data['status'] === 'success') {
            $order->update([
                'payment_status' => 'paid',
                'status' => 'paid',
            ]);
        }

        return response()->json($payment->load('order'), 201);
    }
}
