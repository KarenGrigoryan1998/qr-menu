"use client";
import { useEffect, useMemo, useState } from "react";
import { useCartStore } from "../../store/cart";
import { useI18n } from "../../i18n/I18nContext";
import { useCategoriesQuery, useMenusByCategoryQuery } from "../../lib/queries";
import { CartModal } from "../../components/CartModal";
import { OrdersModal } from "../../components/OrdersModal";
import { ShoppingCartIcon } from "@heroicons/react/24/solid";

export default function TablePage({ params, searchParams }: { params: { tableId: string }; searchParams: Record<string, string | string[] | undefined>; }) {
  const tableId = params.tableId;
  const restaurantId = "1"; // TODO: Get from route or context
  const setTable = useCartStore((s) => s.setTable);
  const addItem = useCartStore((s) => s.addItem);
  const setQty = useCartStore((s) => s.setQty);
  const lines = useCartStore((s) => s.lines);
  const total = useCartStore((s) => s.total());
  const currentOrderId = useCartStore((s) => s.currentOrderId);
  const [activeCat, setActiveCat] = useState<number | null>(null);
  const [showCart, setShowCart] = useState(false);
  const [showOrders, setShowOrders] = useState(false);
  const [restaurant, setRestaurant] = useState<any>(null);
  const [restaurantLoading, setRestaurantLoading] = useState(true);
  const [mounted, setMounted] = useState(false);
  const { t, lang, setLang } = useI18n();
  const locale = lang === "hy" ? "hy-AM" : lang === "ru" ? "ru-RU" : "en-US";
  const { data: categoriesData, isLoading: categoriesLoading } = useCategoriesQuery(lang);
  const { data: menusData, isLoading: menusLoading } = useMenusByCategoryQuery(activeCat ?? undefined, lang);

  // Fetch restaurant data
  useEffect(() => {
    async function fetchRestaurant() {
      try {
        setRestaurantLoading(true);
        const response = await fetch(`${process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080/api'}/restaurants/${restaurantId}/info`);
        const data = await response.json();
        if (data.success) {
          setRestaurant(data.data);
        }
      } catch (error) {
        console.error('Failed to fetch restaurant:', error);
      } finally {
        setRestaurantLoading(false);
      }
    }
    fetchRestaurant();
  }, [restaurantId]);

  // Bind cart to table on mount/switch
  useEffect(() => {
    setTable(tableId, restaurantId);
  }, [setTable, tableId, restaurantId]);

  // Prevent hydration mismatch by delaying client-only values until mounted
  useEffect(() => {
    setMounted(true);
  }, []);

  const cartQty = useMemo(() => lines.reduce((s, l) => s + l.qty, 0), [lines]);

  return (
    <main className="min-h-screen">
      {/* Hero header with background image */}
      <div className="relative h-56 md:h-64 w-full overflow-hidden">
        {/* eslint-disable-next-line @next/next/no-img-element */}
        {!restaurantLoading && (
          <img
            src={restaurant?.image_url || "https://images.unsplash.com/photo-1526318472351-c75fcf070305?q=80&w=1600&auto=format&fit=crop"}
            alt={restaurant?.name || t("restaurantName")}
            className="absolute inset-0 h-full w-full object-cover"
          />
        )}
        {restaurantLoading && (
          <div className="absolute inset-0 bg-neutral-800 shimmer" />
        )}
        <div className="absolute inset-0 bg-gradient-to-b from-black/20 via-black/40 to-black/70" />
        <div className="absolute bottom-4 left-4 right-4 z-10">
          <div className="text-white h1 drop-shadow">
            {restaurantLoading ? (
              <div className="h-6 w-40 bg-white/30 rounded shimmer" />
            ) : (
              restaurant?.name || t("restaurantName")
            )}
          </div>
          <div className="text-white/90 text-sm mt-1">
            {restaurantLoading ? (
              <div className="h-4 w-24 bg-white/20 rounded mt-1 shimmer" />
            ) : (
              <>{t("table")} #{tableId}</>
            )}
          </div>
        </div>
      </div>

      {/* Floating language switcher (top-right) */}
      <div className="fixed top-3 right-3 z-40 flex items-center gap-1">
        <button
          onClick={() => setLang("hy")}
          className={`chip px-2 py-1 ${lang === "hy" ? "bg-rose-50 border-rose-400 text-rose-700 ring-1 ring-rose-300" : "bg-white/80"} backdrop-blur`}
          aria-label="Armenian"
          title="Armenian"
        >🇦🇲</button>
        <button
          onClick={() => setLang("en")}
          className={`chip px-2 py-1 ${lang === "en" ? "bg-rose-50 border-rose-400 text-rose-700 ring-1 ring-rose-300" : "bg-white/80"} backdrop-blur`}
          aria-label="English"
          title="English"
        >🇬🇧</button>
        <button
          onClick={() => setLang("ru")}
          className={`chip px-2 py-1 ${lang === "ru" ? "bg-rose-50 border-rose-400 text-rose-700 ring-1 ring-rose-300" : "bg-white/80"} backdrop-blur`}
          aria-label="Russian"
          title="Russian"
        >🇷🇺</button>
      </div>

      {/* Sticky bar switches between title/back and category pills */}
      <div className="sticky top-0 z-20 bg-[#FFF7F0]/80 backdrop-blur border-b shadow-sm">
        <div className="mx-auto max-w-3xl px-4 py-2 flex items-center gap-2 overflow-x-auto">
          {activeCat ? (
            <>
              <button onClick={() => setActiveCat(null)} className="chip">← {t("backToCategories")}</button>
              <span className="chip bg-rose-50 border-rose-400 text-rose-700 ring-1 ring-rose-300">
                {categoriesData?.find((c) => c.id === activeCat)?.name}
              </span>
            </>
          ) : (
            <>
              {categoriesLoading && (
                <div className="flex gap-2">
                  {Array.from({ length: 6 }).map((_, i) => (
                    <div key={i} className="h-8 w-24 rounded-full bg-rose-100 shimmer" />
                  ))}
                </div>
              )}
              {!categoriesLoading && (categoriesData ?? []).map((c) => (
                <button key={c.id} onClick={() => setActiveCat(c.id)} className="chip whitespace-nowrap">
                  {c.name}
                </button>
              ))}
            </>
          )}
        </div>
      </div>

      <section className="mx-auto max-w-3xl px-4 py-6">
        {!activeCat ? (
          <>
            <h2 className="h2 mb-4 text-white">{t("categories")}</h2>
            <div className="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
              {categoriesLoading && (
                Array.from({ length: 6 }).map((_, i) => (
                  <div key={i} className="group relative overflow-hidden rounded-xl border bg-white shadow-sm shimmer">
                    <div className="h-32 md:h-28 w-full bg-neutral-100" />
                    <div className="p-2">
                      <div className="h-4 w-24 bg-neutral-200 rounded" />
                    </div>
                  </div>
                ))
              )}
              {!categoriesLoading && (categoriesData ?? []).map((c) => (
                <button
                  key={c.id}
                  onClick={() => setActiveCat(c.id)}
                  className="group relative overflow-hidden rounded-xl border bg-white shadow-sm hover:shadow-md transition-shadow"
                >
                  {/* Cover from first item of category or a fallback */}
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={(c as any).image_url ?? "https://images.unsplash.com/photo-1551218808-94e220e084d2?q=80&w=800&auto=format&fit=crop"}
                    alt={c.name}
                    loading="lazy"
                    className="h-32 md:h-28 w-full object-cover transition-transform duration-300 group-hover:scale-[1.03]"
                    onError={(e) => { const img = e.currentTarget; img.onerror = null; img.src = "https://images.unsplash.com/photo-1551218808-94e220e084d2?q=80&w=800&auto=format&fit=crop"; }}
                  />
                  <div className="absolute inset-0 bg-gradient-to-t from-black/70 via-black/30 to-transparent group-hover:from-black/60 transition-colors" />
                  <div className="absolute inset-0 flex items-end justify-center px-3 pb-4 md:pb-5">
                    <div className="text-center">
                      <div className="text-4xl md:text-2xl font-extrabold tracking-tight bg-gradient-to-r from-white via-white to-white bg-clip-text text-transparent drop-shadow-sm transition-transform duration-300 group-hover:scale-105 font-display">
                        {c.name}
                      </div>
                    </div>
                  </div>
                </button>
              ))}
            </div>
          </>
        ) : (
          <>
            <h2 className="h4 mb-4 text-white">{categoriesData?.find((c) => c.id === activeCat)?.name}</h2>
            <div className="grid grid-cols-1 gap-4">
              {menusLoading && (
                Array.from({ length: 4 }).map((_, i) => (
                  <div key={i} className="flex gap-4 card p-3 card-hover shimmer">
                    <div className="h-24 w-24 md:h-28 md:w-28 rounded-lg bg-neutral-200" />
                    <div className="flex-1">
                      <div className="flex items-start justify-between gap-3">
                        <div className="space-y-2 w-full">
                          <div className="h-4 w-40 bg-neutral-200 rounded" />
                          <div className="h-3 w-64 bg-neutral-100 rounded" />
                        </div>
                        <div className="h-4 w-20 bg-rose-100 rounded" />
                      </div>
                      <div className="mt-3 h-8 w-24 bg-gradient-to-r from-rose-600 to-orange-600/80 rounded" />
                    </div>
                  </div>
                ))
              )}
              {!menusLoading && (menusData ?? []).map((it) => (
                <div key={it.id} className="flex gap-4 card p-3 card-hover">
                  {/* eslint-disable-next-line @next/next/no-img-element */}
                  <img
                    src={it.image_url ?? "https://images.unsplash.com/photo-1551218808-94e220e084d2?q=80&w=800&auto=format&fit=crop"}
                    alt={it.name}
                    loading="lazy"
                    className="h-24 w-24 md:h-28 md:w-28 rounded-lg object-cover"
                    onError={(e) => { const img = e.currentTarget; img.onerror = null; img.src = "https://images.unsplash.com/photo-1551218808-94e220e084d2?q=80&w=800&auto=format&fit=crop"; }}
                  />
                  <div className="flex-1">
                    <div className="flex items-start justify-between gap-3">
                      <div className="flex-1">
                        <div className="font-semibold leading-[1.2] bg-gradient-to-r from-rose-600 to-orange-600 bg-clip-text text-transparent text-lg">
                          {it.name}
                        </div>
                        {it.description ? (
                          <div className="text-sm text-gray-600 mt-0.5 leading-[1.35]">{it.description}</div>
                        ) : null}
                      </div>
                      <div className="text-right font-semibold text-rose-700 whitespace-nowrap">
                        {it.price.toLocaleString(locale)} {t("priceCurrency")}
                      </div>
                    </div>
                    <div className="mt-3">
                      {((qty => qty ?? 0)(lines.find(l => l.itemId === String(it.id))?.qty)) === 0 ? (
                        <button
                          onClick={() => addItem({ id: String(it.id), name: it.name, price: Number(it.price) })}
                          className="btn-gradient px-4 py-2 text-sm inline-flex items-center gap-2 rounded-full"
                        >
                          <ShoppingCartIcon className="w-5 h-5 text-white" aria-hidden />
                          <span>{t("add")}</span>
                        </button>
                      ) : (
                        <div className="inline-flex items-center gap-2">
                          <button
                            onClick={() => {
                              const q = (lines.find(l => l.itemId === String(it.id))?.qty ?? 1) - 1;
                              setQty(String(it.id), q);
                            }}
                            className="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 active:bg-gray-300 flex items-center justify-center text-gray-700 font-semibold text-lg"
                            aria-label={t("decrease")}
                          >
                            −
                          </button>
                          <span className="min-w-[2rem] text-center font-semibold text-gray-900">
                            {lines.find(l => l.itemId === String(it.id))?.qty}
                          </span>
                          <button
                            onClick={() => {
                              const q = (lines.find(l => l.itemId === String(it.id))?.qty ?? 0) + 1;
                              setQty(String(it.id), q);
                            }}
                            className="w-9 h-9 rounded-full bg-gray-100 hover:bg-gray-200 active:bg-gray-300 flex items-center justify-center text-gray-700 font-semibold text-lg"
                            aria-label={t("increase")}
                          >
                            +
                          </button>
                        </div>
                      )}
                    </div>
                  </div>
                </div>
              ))}
            </div>
          </>
        )}
      </section>

      {/* Floating action bar */}
      <div className="sticky bottom-0 z-30 border-t border-gray-200 bg-white/80 backdrop-blur shadow-lg pb-safe">
        <div className="mx-auto max-w-3xl px-4 py-3">
          <div className="flex items-center justify-between gap-2 mb-2">
            <div className="text-sm text-gray-700">
              {t("cart")} — <span className="font-semibold text-gray-900" suppressHydrationWarning>{mounted ? cartQty : 0}</span> {t("items")}
            </div>
            <div className="font-bold text-rose-700" suppressHydrationWarning>
              {mounted ? total.toLocaleString(locale) : 0} {t("priceCurrency")}
            </div>
          </div>
          <div className="flex items-center gap-2">
            <button
              onClick={() => setShowCart(true)}
              className="flex-1 btn-gradient rounded-full"
              disabled={cartQty === 0}
            >
              {t("viewCart")}
            </button>
            <button
              onClick={() => setShowOrders(true)}
              className="flex-1 btn-secondary rounded-full"
              disabled={!currentOrderId}
            >
              {t("myOrders")}
            </button>
          </div>
        </div>
      </div>

      {/* Modals */}
      {showCart && <CartModal onClose={() => setShowCart(false)} />}
      {showOrders && <OrdersModal onClose={() => setShowOrders(false)} />}
    </main>
  );
}

