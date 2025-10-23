"use client";
import Link from "next/link";
import { useCartStore } from "../store/cart";
import { useEffect } from "react";
import { useSearchParams } from "next/navigation";
import { useI18n } from "../i18n/I18nContext";

export default function CheckoutPage() {
  const sp = useSearchParams();
  const urlTableId = sp.get("tableId") ?? undefined;
  const tableId = useCartStore((s) => s.tableId) ?? urlTableId ?? "?";
  const lines = useCartStore((s) => s.lines);
  const setQty = useCartStore((s) => s.setQty);
  const setComment = useCartStore((s) => s.setComment);
  const removeItem = useCartStore((s) => s.removeItem);
  const subtotal = useCartStore((s) => s.subtotal());
  const service = useCartStore((s) => s.serviceFee());
  const total = useCartStore((s) => s.total());
  const setTable = useCartStore((s) => s.setTable);
  const { t, lang, setLang } = useI18n();

  const locale = lang === "hy" ? "hy-AM" : lang === "ru" ? "ru-RU" : "en-US";

  useEffect(() => {
    if (urlTableId) setTable(urlTableId);
  }, [setTable, urlTableId]);

  return (
    <main className="min-h-screen bg-white text-gray-900">
      <header className="sticky top-0 z-10 bg-white/80 backdrop-blur border-b">
        <div className="mx-auto max-w-3xl px-4 py-3 flex items-center gap-2">
          <Link href={`/table/${tableId}`} className="text-sm text-gray-600">← {t("back")}</Link>
          <div className="mx-auto text-lg font-semibold">{t("cart")}</div>
          <div className="text-sm text-gray-600 whitespace-nowrap">{t("table")} #{tableId}</div>
          <div className="ml-2 flex items-center gap-1">
            <button
              onClick={() => setLang("hy")}
              className={`chip px-2 py-1 ${lang === "hy" ? "bg-gray-100" : ""}`}
              aria-label="Armenian"
              title="Armenian"
            >🇦🇲</button>
            <button
              onClick={() => setLang("en")}
              className={`chip px-2 py-1 ${lang === "en" ? "bg-gray-100" : ""}`}
              aria-label="English"
              title="English"
            >🇬🇧</button>
            <button
              onClick={() => setLang("ru")}
              className={`chip px-2 py-1 ${lang === "ru" ? "bg-gray-100" : ""}`}
              aria-label="Russian"
              title="Russian"
            >🇷🇺</button>
          </div>
        </div>
      </header>

      <section className="mx-auto max-w-3xl px-4 py-4">
        {lines.length === 0 ? (
          <div className="text-center text-gray-600">{t("emptyCart")}</div>
        ) : (
          <div className="space-y-3">
            {lines.map((l) => (
              <div key={l.itemId} className="card p-3 card-hover">
                <div className="flex items-center justify-between">
                  <div className="font-medium">{l.name}</div>
                  <button onClick={() => removeItem(l.itemId)} className="btn-outline px-3 py-1 text-red-600 border-red-200 hover:bg-red-50">{t("remove")}</button>
                </div>
                <div className="mt-2 flex items-center justify-between">
                  <div className="flex items-center gap-2">
                    <button onClick={() => setQty(l.itemId, l.qty - 1)} className="btn-outline h-8 w-8 p-0">-</button>
                    <div className="w-8 text-center">{l.qty}</div>
                    <button onClick={() => setQty(l.itemId, l.qty + 1)} className="btn-outline h-8 w-8 p-0">+</button>
                  </div>
                  <div className="font-semibold text-rose-700">{(l.price * l.qty).toLocaleString(locale)} {t("priceCurrency")}</div>
                </div>
                <div className="mt-2">
                  <input
                    value={l.comment ?? ""}
                    onChange={(e) => setComment(l.itemId, e.target.value)}
                    placeholder={t("commentPlaceholder")}
                    className="w-full rounded border px-3 py-2 text-sm"
                  />
                </div>
              </div>
            ))}

            <div className="border-t pt-3 text-sm text-gray-700">
              <div className="flex justify-between"><span>{t("subtotal")}</span><span>{subtotal.toLocaleString(locale)} {t("priceCurrency")}</span></div>
              <div className="flex justify-between"><span>{t("serviceFee")}</span><span>{service.toLocaleString(locale)} {t("priceCurrency")}</span></div>
              <div className="flex justify-between font-semibold"><span>{t("grandTotal")}</span><span className="text-rose-700">{total.toLocaleString(locale)} {t("priceCurrency")}</span></div>
            </div>

            <Link href={{ pathname: "/payment", query: { tableId } }} className="btn-primary w-full">
              {t("checkout")}
            </Link>
          </div>
        )}
      </section>
    </main>
  );
}
