"use client";
import { useEffect, useState } from "react";
import { useCartStore } from "../store/cart";
import { useI18n } from "../i18n/I18nContext";
import { orderApi, Order } from "../lib/orderApi";
import { OrderStatusBadge } from "./OrderStatusBadge";
import { PaymentModal } from "./PaymentModal";
import { showToast } from "../lib/toast";

export function OrdersModal({ onClose }: { onClose: () => void }) {
  const { t, lang } = useI18n();
  const { tableId, restaurantId } = useCartStore();
  const [orders, setOrders] = useState<Order[]>([]);
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState<string | null>(null);
  const [selectedOrderForPayment, setSelectedOrderForPayment] = useState<Order | null>(null);
  const [requestingBill, setRequestingBill] = useState<number | null>(null);
  const locale = lang === "hy" ? "hy-AM" : lang === "ru" ? "ru-RU" : "en-US";

  useEffect(() => {
    loadOrders();
  }, []);

  const loadOrders = async () => {
    if (!restaurantId || !tableId) {
      setError("Missing restaurant or table information");
      setLoading(false);
      return;
    }

    setLoading(true);
    setError(null);

    console.log('Fetching orders for:', { restaurantId, tableId });
    const result = await orderApi.getTableOrders(restaurantId, tableId);
    console.log('Orders result:', result);

    if (result.success && result.data) {
      setOrders(result.data);
    } else {
      setError(result.message || "Failed to load orders");
      console.error('Failed to load orders:', result);
    }

    setLoading(false);
  };

  const handleCallWaiter = async (orderId: number) => {
    if (!restaurantId) {
      console.error('Missing restaurantId');
      return;
    }

    setRequestingBill(orderId);
    console.log('Calling waiter for order:', orderId);
    
    const result = await orderApi.requestWaiter(restaurantId, orderId);
    console.log('Call waiter result:', result);

    if (result.success) {
      showToast.success(t("waiterNotified"));
      await loadOrders(); // Reload to show updated status
    } else {
      showToast.error(result.message || "Failed to notify waiter");
      console.error('Call waiter failed:', result);
    }

    setRequestingBill(null);
  };

  const handlePaymentSuccess = () => {
    setSelectedOrderForPayment(null);
    loadOrders();
    showToast.success(t("paymentSuccess"));
  };

  return (
    <>
      <div
        className="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-fade-in"
        onClick={onClose}
      >
        <div
          className="bg-white rounded-t-2xl sm:rounded-2xl w-full sm:max-w-2xl max-h-[90vh] flex flex-col animate-slide-up sm:animate-scale-in"
          onClick={(e) => e.stopPropagation()}
        >
          {/* Header */}
          <div className="flex items-center justify-between p-4 border-b sticky top-0 bg-white rounded-t-2xl shadow-sm">
            <h2 className="text-xl font-bold">{t("myOrders")}</h2>
            <button
              onClick={onClose}
              className="text-gray-500 hover:text-gray-700 text-2xl leading-none"
              aria-label="Close"
            >
              ×
            </button>
          </div>

          {/* Content */}
          <div className="flex-1 overflow-y-auto p-4">
            {loading ? (
              <div className="text-center py-12">
                <div className="text-4xl mb-2">⏳</div>
                <div className="text-gray-500">Loading orders...</div>
              </div>
            ) : error ? (
              <div className="text-center py-12">
                <div className="text-4xl mb-2">⚠️</div>
                <div className="text-red-600">{error}</div>
                <button onClick={loadOrders} className="mt-4 btn-gradient">
                  Retry
                </button>
              </div>
            ) : orders.length === 0 ? (
              <div className="text-center py-12 text-gray-500">
                <div className="text-4xl mb-2">📋</div>
                <div>{t("noActiveOrders")}</div>
              </div>
            ) : (
              <div className="space-y-4">
                {orders.map((order) => (
                  <div key={order.id} className="card p-4">
                    {/* Order header */}
                    <div className="flex items-start justify-between mb-3">
                      <div>
                        <div className="font-bold text-lg text-gray-900">
                          {t("order")} #{order.id}
                        </div>
                        <div className="text-sm text-gray-600">
                          {new Date(order.created_at).toLocaleString(locale)}
                        </div>
                      </div>
                      <OrderStatusBadge status={order.status} />
                    </div>

                    {/* Order items */}
                    <div className="space-y-2 mb-3">
                      {order.items.map((item) => (
                        <div
                          key={item.id}
                          className="flex justify-between text-sm border-b border-gray-200 pb-2"
                        >
                          <div className="flex-1">
                            <div className="font-medium text-gray-900">{item.menu.name}</div>
                            {item.comment && (
                              <div className="text-xs text-gray-600 italic mt-0.5">
                                {item.comment}
                              </div>
                            )}
                          </div>
                          <div className="text-right">
                            <div className="text-gray-700">
                              {item.price.toLocaleString(locale)} {t("priceCurrency")} × {item.quantity}
                            </div>
                            <div className="font-semibold text-gray-900">
                              {(item.price * item.quantity).toLocaleString(locale)} {t("priceCurrency")}
                            </div>
                          </div>
                        </div>
                      ))}
                    </div>

                    {/* Order total */}
                    <div className="flex justify-between font-bold text-lg pt-2 border-t border-gray-300">
                      <span className="text-gray-900">{t("grandTotal")}</span>
                      <span className="text-rose-700">
                        {order.total_amount.toLocaleString(locale)} {t("priceCurrency")}
                      </span>
                    </div>

                    {/* Payment status */}
                    <div className="mt-2 text-sm">
                      <span className="text-gray-600">{t("payment")}: </span>
                      <span
                        className={
                          order.payment_status === "paid"
                            ? "text-green-600 font-semibold"
                            : "text-orange-600 font-semibold"
                        }
                      >
                        {order.payment_status === "paid" ? "✓ " : ""}
                        {t(order.payment_status)}
                      </span>
                    </div>

                    {/* Actions */}
                    {order.payment_status === "unpaid" && (
                      <div className="mt-4 space-y-2">
                        {/* Primary Action: Pay Now - Always visible */}
                        <button
                          onClick={() => setSelectedOrderForPayment(order)}
                          className="w-full btn-gradient flex items-center justify-center gap-2"
                        >
                          <span>💳</span>
                          <span>{t("payNow")}</span>
                        </button>
                        
                        {/* Secondary Action: Call Waiter - For assistance */}
                        <button
                          onClick={() => handleCallWaiter(order.id)}
                          className="w-full btn-secondary flex items-center justify-center gap-2 text-sm"
                          disabled={requestingBill === order.id}
                        >
                          <span>🔔</span>
                          <span>{requestingBill === order.id ? "..." : t("callWaiter")}</span>
                        </button>
                      </div>
                    )}
                    
                    {/* Paid status */}
                    {order.payment_status === "paid" && (
                      <div className="mt-4 p-3 bg-green-50 border border-green-200 rounded-lg">
                        <div className="text-green-700 font-semibold text-center">
                          ✓ {t("paymentSuccess")}
                        </div>
                        <div className="text-sm text-green-600 mt-1 text-center">
                          {t("thankYou")}
                        </div>
                        <div className="text-xs text-gray-500 mt-2 text-center">
                          {t("orderWillDisappear")}
                        </div>
                      </div>
                    )}
                  </div>
                ))}
              </div>
            )}
          </div>

          {/* Footer */}
          <div className="border-t p-4 bg-gray-50 rounded-b-2xl">
            <button onClick={onClose} className="w-full btn-secondary">
              {t("back")}
            </button>
          </div>
        </div>
      </div>

      {/* Payment Modal */}
      {selectedOrderForPayment && (
        <PaymentModal
          order={selectedOrderForPayment}
          onClose={() => setSelectedOrderForPayment(null)}
          onSuccess={handlePaymentSuccess}
        />
      )}
    </>
  );
}
