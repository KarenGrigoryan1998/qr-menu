"use client";
import { useOrdersStore } from "../store/orders";

export default function KitchenPage() {
  const orders = useOrdersStore((s) => s.orders);
  const setStatus = useOrdersStore((s) => s.setStatus);

  return (
    <main className="min-h-screen bg-white text-gray-900">
      <header className="sticky top-0 z-10 bg-white/80 backdrop-blur border-b">
        <div className="mx-auto max-w-4xl px-4 py-3 flex items-center justify-between">
          <div className="text-lg font-semibold">Խոհանոց</div>
          <div className="text-sm text-gray-600">{orders.length} պատվեր</div>
        </div>
      </header>

      <section className="mx-auto max-w-4xl px-4 py-6 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        {orders.length === 0 ? (
          <div className="text-gray-600">Դեռ պատվեր չկա</div>
        ) : (
          orders.map((o) => (
            <div key={o.id} className="card p-4 card-hover">
              <div className="flex items-center justify-between">
                <div className="font-semibold">Պատվեր #{o.id}</div>
                <div className="text-xs rounded-full px-2 py-0.5 border">
                  {o.status === "queued" ? "Սպասման մեջ" : o.status === "preparing" ? "Պատրաստվում է" : "Պատրաստ"}
                </div>
              </div>
              <div className="text-sm text-gray-600">Սեղան #{o.tableId}</div>
              <div className="mt-2 space-y-1">
                {o.lines.map((i) => (
                  <div key={i.itemId} className="flex justify-between">
                    <span>{i.name}</span>
                    <span className="font-medium">x{i.qty}</span>
                  </div>
                ))}
              </div>
              <div className="mt-2 text-right font-semibold text-rose-700">{o.total.toLocaleString("hy-AM")} ֏</div>
              <div className="mt-3 grid grid-cols-2 gap-2">
                <button onClick={() => setStatus(o.id, "preparing")} className="btn-outline">Սկսել</button>
                <button onClick={() => setStatus(o.id, "done")} className="btn-primary">Պատրաստ</button>
              </div>
            </div>
          ))
        )}
      </section>
    </main>
  );
}
