<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index()
    {
        return Order::with(['table', 'items.menu'])->orderByDesc('id')->get();
    }

    public function show(Order $order)
    {
        return $order->load(['table', 'items.menu', 'payments']);
    }

    public function store(Request $request, OrderService $svc)
    {
        $data = $request->validate([
            'table_id' => ['required', 'integer', 'exists:tables,id'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.menu_id' => ['required', 'integer', 'exists:menus,id'],
            'items.*.quantity' => ['required', 'integer', 'min:1'],
            'items.*.comment' => ['nullable', 'string'],
        ]);

        $order = $svc->create($data['table_id'], $data['items']);
        return response()->json($order, 201);
    }

    public function updateStatus(Request $request, Order $order)
    {
        $validated = $request->validate([
            'status' => ['required', 'in:pending,paid,cooking,ready,delivered,closed'],
        ]);
        $order->update(['status' => $validated['status']]);
        return $order->refresh();
    }
}
