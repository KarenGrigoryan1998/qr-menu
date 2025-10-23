"use client";
import { useState } from "react";
import { useCartStore } from "../store/cart";
import { useI18n } from "../i18n/I18nContext";
import { orderApi } from "../lib/orderApi";
import { showToast } from "../lib/toast";
import { TrashIcon } from "@heroicons/react/24/outline";

export function CartModal({ onClose }: { onClose: () => void }) {
  const { t, lang } = useI18n();
  const {
    lines,
    setQty,
    setComment,
    removeItem,
    clearOrder,
    subtotal,
    serviceFee,
    total,
    tableId,
    restaurantId,
    setCurrentOrder,
  } = useCartStore();
  const [isSubmitting, setIsSubmitting] = useState(false);
  const [error, setError] = useState<string | null>(null);
  const locale = lang === "hy" ? "hy-AM" : lang === "ru" ? "ru-RU" : "en-US";

  const handlePlaceOrder = async () => {
    if (!restaurantId || !tableId) {
      setError("Missing restaurant or table information");
      return;
    }

    if (lines.length === 0) {
      setError(t("emptyCart"));
      return;
    }

    setIsSubmitting(true);
    setError(null);

    const items = lines.map((line) => ({
      menu_id: parseInt(line.itemId),
      quantity: line.qty,
      comment: line.comment || undefined,
    }));

    const result = await orderApi.createOrder(restaurantId, tableId, items);

    if (result.success && result.data) {
      setCurrentOrder(result.data.id);
      clearOrder();
      // Show success message with payment guidance
      const orderNumber = result.data.id;
      showToast.successPersistent(
        `${t("orderPlacedSuccess")}\n${t("order")} #${orderNumber}\n💡 ${t("paymentGuidance")}`
      );
      onClose();
    } else {
      setError(result.message || "Failed to place order");
      showToast.error(result.message || "Failed to place order");
    }

    setIsSubmitting(false);
  };

  return (
    <div
      className="fixed inset-0 z-50 bg-black/40 backdrop-blur-sm flex items-end sm:items-center justify-center p-0 sm:p-4 animate-fade-in"
      onClick={onClose}
    >
      <div
        className="bg-white rounded-t-2xl sm:rounded-2xl w-full sm:max-w-lg max-h-[90vh] flex flex-col animate-slide-up sm:animate-scale-in"
        onClick={(e) => e.stopPropagation()}
      >
        {/* Header */}
        <div className="flex items-center justify-between p-4 border-b sticky top-0 bg-white rounded-t-2xl sm:rounded-t-2xl shadow-sm">
          <h2 className="text-xl font-bold">{t("cart")}</h2>
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
          {lines.length === 0 ? (
            <div className="text-center py-12 text-gray-500">
              <div className="text-4xl mb-2">🛒</div>
              <div>{t("emptyCart")}</div>
            </div>
          ) : (
            <div className="space-y-4">
              {lines.map((line) => (
                <div key={line.itemId} className="card p-3">
                  <div className="flex items-start gap-3">
                    <div className="flex-1">
                      <div className="font-semibold text-gray-900">{line.name}</div>
                      <div className="text-sm text-gray-600 mt-1">
                        {line.price.toLocaleString(locale)} {t("priceCurrency")} × {line.qty}
                      </div>
                      
                      {/* Comment input */}
                      <input
                        type="text"
                        placeholder={t("commentPlaceholder")}
                        value={line.comment || ""}
                        onChange={(e) => setComment(line.itemId, e.target.value)}
                        className="mt-2 w-full px-3 py-2 text-sm text-black border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-rose-500 focus:border-transparent"
                      />
                    </div>

                    {/* Quantity controls */}
                    <div className="flex items-center gap-2">
                      <button
                        onClick={() => setQty(line.itemId, line.qty - 1)}
                        className="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 active:bg-gray-300 flex items-center justify-center text-gray-700 font-semibold text-lg"
                        disabled={isSubmitting}
                      >
                        −
                      </button>
                      <span className="w-8 text-center font-semibold text-gray-900">{line.qty}</span>
                      <button
                        onClick={() => setQty(line.itemId, line.qty + 1)}
                        className="w-8 h-8 rounded-full bg-gray-100 hover:bg-gray-200 active:bg-gray-300 flex items-center justify-center text-gray-700 font-semibold text-lg"
                        disabled={isSubmitting}
                      >
                        +
                      </button>
                    </div>

                    {/* Remove button */}
                    <button
                      onClick={() => removeItem(line.itemId)}
                      className="w-8 h-8 rounded-full flex items-center justify-center text-red-600 hover:text-red-700 hover:bg-red-50 active:bg-red-100"
                      aria-label={t("remove")}
                      title={t("remove")}
                      disabled={isSubmitting}
                    >
                      <TrashIcon className="w-5 h-5" aria-hidden />
                    </button>
                  </div>

                  {/* Line total */}
                  <div className="text-right mt-2 font-semibold text-rose-700">
                    {(line.price * line.qty).toLocaleString(locale)} {t("priceCurrency")}
                  </div>
                </div>
              ))}
            </div>
          )}
        </div>

        {/* Footer with totals and actions */}
        {lines.length > 0 && (
          <div className="border-t border-gray-200 p-4 bg-gray-50 rounded-b-2xl sm:rounded-b-2xl">
            <div className="space-y-2 mb-4">
              <div className="flex justify-between text-sm text-gray-700">
                <span>{t("subtotal")}</span>
                <span className="font-medium">{subtotal().toLocaleString(locale)} {t("priceCurrency")}</span>
              </div>
              <div className="flex justify-between text-sm text-gray-600">
                <span>{t("serviceFee")}</span>
                <span className="font-medium">{serviceFee().toLocaleString(locale)} {t("priceCurrency")}</span>
              </div>
              <div className="flex justify-between font-bold text-lg pt-2 border-t border-gray-300">
                <span className="text-gray-900">{t("grandTotal")}</span>
                <span className="text-rose-700">
                  {total().toLocaleString(locale)} {t("priceCurrency")}
                </span>
              </div>
            </div>

            {error && (
              <div className="mb-3 p-2 bg-red-50 text-red-700 text-sm rounded">
                {error}
              </div>
            )}

            <div className="flex gap-2">
              <button
                onClick={onClose}
                className="flex-1 btn-secondary"
                disabled={isSubmitting}
              >
                {t("continueShopping")}
              </button>
              <button
                onClick={handlePlaceOrder}
                className="flex-1 btn-gradient"
                disabled={isSubmitting}
              >
                {isSubmitting ? "..." : t("placeOrder")}
              </button>
            </div>
          </div>
        )}
      </div>
    </div>
  );
}
