"use client";
import { useState } from "react";
import { useCartStore } from "../store/cart";
import { useI18n } from "../i18n/I18nContext";
import { orderApi, Order } from "../lib/orderApi";
import { showToast } from "../lib/toast";

export function PaymentModal({
  order,
  onClose,
  onSuccess,
}: {
  order: Order;
  onClose: () => void;
  onSuccess: () => void;
}) {
  const { t, lang } = useI18n();
  const { restaurantId } = useCartStore();
  const [method, setMethod] = useState<string>("cash");
  const [isProcessing, setIsProcessing] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const locale = lang === "hy" ? "hy-AM" : lang === "ru" ? "ru-RU" : "en-US";

  const paymentMethods = [
    { value: "cash", label: t("cash"), icon: "💵" },
    { value: "card", label: t("card"), icon: "💳" },
    { value: "idram", label: "Idram", icon: "📱" },
    { value: "telcell", label: "Telcell", icon: "📱" },
  ];

  const handlePayment = async () => {
    if (!restaurantId) {
      setError("Missing restaurant information");
      return;
    }

    setIsProcessing(true);
    setError(null);

    // For card/online payments, generate mock transaction ID
    const transactionId =
      method !== "cash" ? `TXN_${Date.now()}_${Math.random().toString(36).substr(2, 9)}` : undefined;

    const result = await orderApi.processPayment(
      restaurantId,
      method,
      transactionId
    );

    if (result.success) {
      showToast.success(t("paymentSuccess"));
      onSuccess();
    } else {
      setError(result.message || "Payment failed");
      showToast.error(result.message || t("paymentFailed"));
    }
    setIsProcessing(false);
  };

  return (
    <div
      className="fixed inset-0 z-[60] bg-black/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-fade-in"
      onClick={onClose}
    >
      <div
        className="bg-white rounded-t-2xl sm:rounded-2xl w-full sm:max-w-md flex flex-col animate-slide-up sm:animate-scale-in"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="flex items-center justify-between p-4 border-b shadow-sm">
          <h2 className="text-xl font-bold">{t("payment")}</h2>
          <button
            onClick={onClose}
            className="text-gray-500 hover:text-gray-700 text-2xl leading-none"
            aria-label="Close"
            disabled={isProcessing}
          >
            ×
          </button>
        </div>

        {/* Content */}
        <div className="p-4 space-y-4">
          {/* Order summary */}
          <div className="card p-4 bg-gray-50 border-gray-200">
            <div className="text-sm text-gray-600 mb-2">
              {t("order")} #{order.id}
            </div>
            <div className="flex justify-between items-center">
              <span className="font-semibold text-gray-900">{t("grandTotal")}</span>
              <span className="text-2xl font-bold text-rose-700">
                {order.total_amount.toLocaleString(locale)} {t("priceCurrency")}
              </span>
            </div>
          </div>

          {/* Payment method selection */}
          <div>
            <label className="block text-sm font-semibold text-gray-900 mb-2">
              {t("choosePaymentMethod")}
            </label>
            <div className="grid grid-cols-2 gap-2">
              {paymentMethods.map((pm) => (
                <button
                  key={pm.value}
                  onClick={() => setMethod(pm.value)}
                  disabled={isProcessing}
                  className={`p-3 rounded-lg border-2 transition-all ${
                    method === pm.value
                      ? "border-rose-300 bg-gradient-to-r from-rose-50 to-orange-50 shadow-sm"
                      : "border-gray-200 hover:border-gray-300 bg-white"
                  }`}
                >
                  <div className="text-2xl mb-1">{pm.icon}</div>
                  <div className={`text-sm font-medium ${
                    method === pm.value ? "text-rose-700" : "text-gray-900"
                  }`}>{pm.label}</div>
                </button>
              ))}
            </div>
          </div>

          {/* Info message for online payments */}
          {method !== "cash" && (
            <div className="text-xs text-gray-700 bg-amber-50 border border-amber-200 p-3 rounded-lg">
              <span className="font-semibold">ℹ️ Demo Mode:</span> This will simulate a successful payment. In production,
              this would redirect to ConverseBank VPOS.
            </div>
          )}

          {/* Error message */}
          {error && (
            <div className="p-3 bg-red-50 border border-red-200 text-red-700 text-sm rounded-lg font-medium">
              ⚠️ {error}
            </div>
          )}
        </div>

        {/* Footer */}
        <div className="border-t p-4 space-y-2">
          <button
            onClick={handlePayment}
            className="w-full btn-gradient"
            disabled={isProcessing}
          >
            {isProcessing ? (
              <span className="flex items-center justify-center gap-2">
                <span className="animate-spin">⏳</span>
                Processing...
              </span>
            ) : (
              `${t("payNow")} ${order.total_amount.toLocaleString(locale)} ${t("priceCurrency")}`
            )}
          </button>
          <button
            onClick={onClose}
            className="w-full btn-secondary"
            disabled={isProcessing}
          >
            {t("back")}
          </button>
        </div>
      </div>
    </div>
  );
}
