"use client";
import Link from "next/link";
import { useSearchParams } from "next/navigation";

export default function SuccessPage() {
  const sp = useSearchParams();
  const orderId = sp.get("orderId") ?? "-";
  const tableId = sp.get("tableId") ?? "?";

  return (
    <main className="min-h-screen bg-white text-gray-900">
      <header className="sticky top-0 z-10 bg-white/80 backdrop-blur border-b">
        <div className="mx-auto max-w-3xl px-4 py-3 flex items-center justify-center">
          <div className="text-lg font-semibold">Հաջող վճարում</div>
        </div>
      </header>
      <section className="mx-auto max-w-3xl px-4 py-12 text-center">
        <div className="text-2xl font-bold mb-2">Վճարումը հաջողվեց</div>
        <div className="text-gray-700 mb-6">Պատվեր #{orderId} ուղարկվեց խոհանոց</div>
        <Link href={`/table/${tableId}`} className="btn-primary">Շարունակել պատվիրել</Link>
      </section>
    </main>
  );
}
