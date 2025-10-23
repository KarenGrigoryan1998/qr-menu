<?php

namespace App\Http\Controllers\Api;

use App\Exceptions\OrderException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $orderService
    ) {}

    /**
     * Create a new order
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'table_id' => 'required|integer|exists:tables,id',
                'items' => 'required|array|min:1',
                'items.*.menu_id' => 'required|integer|exists:menus,id',
                'items.*.quantity' => 'required|integer|min:1|max:99',
                'items.*.comment' => 'nullable|string|max:500',
            ]);

            $restaurantId = (int) $request->route('restaurant');

            $order = $this->orderService->createOrder(
                $restaurantId,
                $validated['table_id'],
                $validated['items']
            );

            return response()->json([
                'success' => true,
                'message' => 'Order created successfully',
                'data' => $order,
            ], 201);

        } catch (OrderException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);

        } catch (\Exception $e) {
            Log::error('Order creation failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to create order. Please try again.',
            ], 500);
        }
    }

    /**
     * Add items to existing order
     */
    public function addItems(Request $request, string $restaurant, int $orderId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'items' => 'required|array|min:1',
                'items.*.menu_id' => 'required|integer|exists:menus,id',
                'items.*.quantity' => 'required|integer|min:1|max:99',
                'items.*.comment' => 'nullable|string|max:500',
            ]);

            $order = Order::where('id', $orderId)
                ->where('restaurant_id', (int) $restaurant)
                ->firstOrFail();

            $order = $this->orderService->addItemsToOrder($order, $validated['items']);

            return response()->json([
                'success' => true,
                'message' => 'Items added successfully',
                'data' => $order,
            ]);

        } catch (OrderException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);

        } catch (\Exception $e) {
            Log::error('Add items failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to add items. Please try again.',
            ], 500);
        }
    }

    /**
     * Get order details
     */
    public function show(string $restaurant, int $orderId): JsonResponse
    {
        try {
            $order = $this->orderService->getOrderDetails($orderId);

            return response()->json([
                'success' => true,
                'data' => $order,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Order not found',
            ], 404);
        }
    }

    /**
     * Get active orders for a table
     */
    public function tableOrders(string $restaurant, int $tableId): JsonResponse
    {
        try {
            $restaurantId = (int) $restaurant;

            // Validate table belongs to restaurant
            $table = \App\Models\Table::where('id', $tableId)
                ->where('restaurant_id', $restaurantId)
                ->first();

            if (!$table) {
                return response()->json([
                    'success' => false,
                    'message' => 'Table not found',
                ], 404);
            }

            $orders = $this->orderService->getActiveOrdersForTable($tableId);

            return response()->json([
                'success' => true,
                'data' => $orders,
            ]);

        } catch (\Exception $e) {
            Log::error('Get table orders failed', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'restaurant' => $restaurant,
                'tableId' => $tableId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch orders',
                'error' => config('app.debug') ? $e->getMessage() : null,
            ], 500);
        }
    }

    /**
     * Request waiter assistance for an order
     * This notifies staff but doesn't change order status
     */
    public function requestWaiter(string $restaurant, int $orderId): JsonResponse
    {
        try {
            $order = Order::where('id', $orderId)
                ->where('restaurant_id', (int) $restaurant)
                ->firstOrFail();

            // Create waiter request in database
            $waiterRequest = \App\Models\WaiterRequest::create([
                'restaurant_id' => (int) $restaurant,
                'table_id' => $order->table_id,
                'order_id' => $order->id,
                'status' => 'pending',
                'note' => 'Customer requested waiter',
            ]);

            // Log for monitoring
            Log::info('Waiter requested', [
                'request_id' => $waiterRequest->id,
                'order_id' => $orderId,
                'restaurant_id' => $restaurant,
                'table_id' => $order->table_id,
            ]);

            // TODO: Send real-time notification
            // - Broadcast event to staff dashboard
            // - Send push notification to staff app
            // - Optional: SMS to manager

            return response()->json([
                'success' => true,
                'message' => 'Waiter has been notified',
                'data' => [
                    'order' => $order->fresh()->load('items.menu'),
                    'request_id' => $waiterRequest->id,
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Call waiter failed', [
                'error' => $e->getMessage(),
                'order_id' => $orderId,
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to notify waiter',
            ], 500);
        }
    }

    /**
     * Update order status
     */
    public function updateStatus(Request $request, string $restaurant, int $orderId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'status' => 'required|in:confirmed,preparing,ready,served,completed,cancelled',
            ]);

            $order = Order::where('id', $orderId)
                ->where('restaurant_id', (int) $restaurant)
                ->firstOrFail();

            $order = $this->orderService->updateStatus($order, $validated['status']);

            return response()->json([
                'success' => true,
                'message' => 'Order status updated',
                'data' => $order,
            ]);

        } catch (OrderException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);

        } catch (\Exception $e) {
            Log::error('Update status failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Failed to update status',
            ], 500);
        }
    }

    /**
     * Process payment for an order
     */
    public function processPayment(Request $request, string $restaurant, int $orderId): JsonResponse
    {
        try {
            $validated = $request->validate([
                'payment_method' => 'required|in:cash,card,idram,telcell,visa,mastercard',
                'transaction_id' => 'nullable|string|max:255',
            ]);

            $order = Order::where('id', $orderId)
                ->where('restaurant_id', (int) $restaurant)
                ->firstOrFail();

            $order = $this->orderService->markAsPaid(
                $order,
                $validated['payment_method'],
                $validated['transaction_id'] ?? null
            );

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'data' => $order,
            ]);

        } catch (OrderException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);

        } catch (\Exception $e) {
            Log::error('Payment processing failed', ['error' => $e->getMessage()]);

            return response()->json([
                'success' => false,
                'message' => 'Payment processing failed',
            ], 500);
        }
    }
}
