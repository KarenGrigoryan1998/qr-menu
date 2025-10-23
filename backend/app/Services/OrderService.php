<?php

namespace App\Services;

use App\Exceptions\OrderException;
use App\Models\Menu;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Table;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class OrderService
{
    /**
     * Create an order with items and compute totals.
     * @param int $restaurantId
     * @param int $tableId
     * @param array<int, array{menu_id:int, quantity:int, comment?:string}> $items
     * @throws OrderException
     */
    public function createOrder(int $restaurantId, int $tableId, array $items): Order
    {
        if (empty($items)) {
            throw OrderException::emptyCart();
        }

        $table = Table::where('id', $tableId)
            ->where('restaurant_id', $restaurantId)
            ->firstOrFail();

        // Check table availability (optional - can be 'occupied' during order)
        if ($table->status === 'reserved') {
            throw OrderException::tableNotAvailable();
        }

        return DB::transaction(function () use ($restaurantId, $tableId, $items, $table) {
            // Mark table as occupied
            $table->update(['status' => 'occupied']);

            $order = Order::create([
                'restaurant_id' => $restaurantId,
                'table_id' => $tableId,
                'status' => 'pending',
                'payment_status' => 'unpaid',
                'total_amount' => 0,
            ]);

            $subtotal = 0;
            foreach ($items as $item) {
                $menu = Menu::where('id', $item['menu_id'])
                    ->where('restaurant_id', $restaurantId)
                    ->where('available', true)
                    ->firstOrFail();

                $qty = max(1, (int)($item['quantity'] ?? 1));
                $lineTotal = $menu->price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'quantity' => $qty,
                    'price' => $menu->price,
                    'comment' => $item['comment'] ?? null,
                ]);

                $subtotal += $lineTotal;
            }

            // Add 10% service fee
            $serviceFee = round($subtotal * 0.10, 2);
            $total = $subtotal + $serviceFee;

            $order->update(['total_amount' => $total]);

            Log::info('Order created', ['order_id' => $order->id, 'table_id' => $tableId, 'total' => $total]);

            return $order->load(['items.menu', 'table']);
        });
    }

    /**
     * Add items to existing order (if still pending)
     * @throws OrderException
     */
    public function addItemsToOrder(Order $order, array $items): Order
    {
        if (!in_array($order->status, ['pending', 'confirmed'])) {
            throw OrderException::cannotModifyOrder();
        }

        return DB::transaction(function () use ($order, $items) {
            $additionalTotal = 0;

            foreach ($items as $item) {
                $menu = Menu::where('id', $item['menu_id'])
                    ->where('restaurant_id', $order->restaurant_id)
                    ->where('available', true)
                    ->firstOrFail();

                $qty = max(1, (int)($item['quantity'] ?? 1));
                $lineTotal = $menu->price * $qty;

                OrderItem::create([
                    'order_id' => $order->id,
                    'menu_id' => $menu->id,
                    'quantity' => $qty,
                    'price' => $menu->price,
                    'comment' => $item['comment'] ?? null,
                ]);

                $additionalTotal += $lineTotal;
            }

            // Recalculate total with service fee
            $order->refresh();
            $subtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);
            $serviceFee = round($subtotal * 0.10, 2);
            $total = $subtotal + $serviceFee;

            $order->update(['total_amount' => $total]);

            Log::info('Items added to order', ['order_id' => $order->id, 'additional' => $additionalTotal]);

            return $order->load(['items.menu', 'table']);
        });
    }

    /**
     * Update order status following state machine
     * pending -> confirmed -> preparing -> ready -> served -> completed
     * @throws OrderException
     */
    public function updateStatus(Order $order, string $newStatus): Order
    {
        $validTransitions = [
            'pending' => ['confirmed', 'cancelled'],
            'confirmed' => ['preparing', 'cancelled'],
            'preparing' => ['ready', 'cancelled'],
            'ready' => ['served'],
            'served' => ['completed'],
            'completed' => [],
            'cancelled' => [],
        ];

        $currentStatus = $order->status;

        if (!isset($validTransitions[$currentStatus]) || !in_array($newStatus, $validTransitions[$currentStatus])) {
            throw OrderException::invalidStatus($currentStatus, $newStatus);
        }

        $order->update(['status' => $newStatus]);

        Log::info('Order status updated', ['order_id' => $order->id, 'from' => $currentStatus, 'to' => $newStatus]);

        return $order->fresh();
    }

    /**
     * Request bill / mark ready for payment
     */
    public function requestBill(Order $order): Order
    {
        if ($order->payment_status === 'paid') {
            throw OrderException::alreadyPaid();
        }

        // Mark as served if not already
        if (!in_array($order->status, ['served', 'completed'])) {
            $order->update(['status' => 'served']);
        }

        Log::info('Bill requested', ['order_id' => $order->id]);

        return $order->fresh();
    }

    /**
     * Mark order as paid
     * @throws OrderException
     */
    public function markAsPaid(Order $order, string $paymentMethod, ?string $transactionId = null): Order
    {
        if ($order->payment_status === 'paid') {
            throw OrderException::alreadyPaid();
        }

        return DB::transaction(function () use ($order, $paymentMethod, $transactionId) {
            // Mark as paid but keep status as 'served' or current status
            // Don't set to 'completed' yet - let staff close it manually
            $order->update([
                'payment_status' => 'paid',
                // Keep current status, don't auto-complete
            ]);

            // Create payment record
            $order->payments()->create([
                'restaurant_id' => $order->restaurant_id,
                'amount' => $order->total_amount,
                'method' => $paymentMethod,
                'status' => 'success',
                'transaction_id' => $transactionId,
                'paid_at' => now(),
            ]);

            // Note: Don't free table yet - customer might still be sitting
            // Table will be freed when staff marks order as 'completed'

            Log::info('Order paid', ['order_id' => $order->id, 'method' => $paymentMethod]);

            return $order->fresh(['items.menu', 'table', 'payments']);
        });
    }

    /**
     * Get order with full details
     */
    public function getOrderDetails(int $orderId): Order
    {
        return Order::with(['items.menu', 'table', 'payments', 'restaurant'])
            ->findOrFail($orderId);
    }

    /**
     * Get active orders for a table
     * Shows all orders that are not closed
     * Actual statuses: pending, paid, cooking, ready, delivered, closed
     */
    public function getActiveOrdersForTable(int $tableId): \Illuminate\Database\Eloquent\Collection
    {
        return Order::with(['items.menu', 'payments'])
            ->where('table_id', $tableId)
            ->where('status', '!=', 'closed')
            ->orderBy('created_at', 'desc')
            ->get();
    }
}
