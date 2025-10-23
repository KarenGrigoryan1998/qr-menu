"use client";
import Link from "next/link";
import { useRouter, useSearchParams } from "next/navigation";
import { useCartStore } from "../store/cart";
import { useOrdersStore } from "../store/orders";

export default function PaymentPage() {
  const router = useRouter();
  const sp = useSearchParams();
  const urlTableId = sp.get("tableId") ?? undefined;
  const cartTableId = useCartStore((s) => s.tableId);
  const tableId = urlTableId ?? cartTableId ?? "?";
  const lines = useCartStore((s) => s.lines);
  const total = useCartStore((s) => s.total());
  const clear = useCartStore((s) => s.clear);
  const addOrder = useOrdersStore((s) => s.addOrder);

  const createOrderAndRedirect = () => {
    const orderId = `${Date.now()}`;
    addOrder({
      id: orderId,
      tableId: String(tableId),
      lines: [...lines],
      total,
      createdAt: Date.now(),
      status: "queued",
    });
    clear();
    router.push(`/success?tableId=${tableId}&orderId=${orderId}`);
  };

  const pay = (status: "success" | "fail") => {
    if (status === "success") createOrderAndRedirect();
    else alert("Վճարումը ձախողվեց, խնդրում ենք փորձել կրկին");
  };

  return (
    <main className="min-h-screen bg-white text-gray-900">
      <header className="sticky top-0 z-10 bg-white/80 backdrop-blur border-b">
        <div className="mx-auto max-w-3xl px-4 py-3 flex items-center justify-between">
          <Link href={`/checkout?tableId=${tableId}`} className="text-sm text-gray-600">← Վերադառնալ</Link>
          <div className="text-lg font-semibold">Վճարում</div>
          <div className="text-sm text-rose-700">Ընդհանուր: {total.toLocaleString("hy-AM")} ֏</div>
        </div>
      </header>

      <section className="mx-auto max-w-3xl px-4 py-6 space-y-3">
        <div className="card p-4 card-hover">
          <div className="font-medium mb-2">Ընտրեք վճարման եղանակը</div>
          <div className="grid grid-cols-1 gap-2">
            <button onClick={() => pay("success")} className="btn-primary">Idram (mock)</button>
            <button onClick={() => pay("success")} className="btn-primary">Telcell (mock)</button>
            <button onClick={() => pay("success")} className="btn-primary">Կրեդիտ քարտ (mock)</button>
            <button onClick={() => pay("fail")} className="btn-outline">Սիմուլացնել ձախողում</button>
          </div>
        </div>
      </section>
    </main>
  );
}
