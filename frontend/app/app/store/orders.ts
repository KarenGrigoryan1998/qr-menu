"use client";
import { create } from "zustand";
import { persist, createJSONStorage } from "zustand/middleware";
import type { CartLine } from "./cart";

export type Order = {
  id: string; // e.g., timestamp-based id
  tableId: string;
  lines: CartLine[];
  total: number;
  createdAt: number;
  status: "queued" | "preparing" | "done";
};

type OrdersState = {
  orders: Order[];
  addOrder: (o: Order) => void;
  setStatus: (id: string, status: Order["status"]) => void;
  clearAll: () => void;
};

export const useOrdersStore = create<OrdersState>()(
  persist(
    (set) => ({
      orders: [],
      addOrder: (o) => set((s) => ({ orders: [o, ...s.orders] })),
      setStatus: (id, status) =>
        set((s) => ({ orders: s.orders.map((o) => (o.id === id ? { ...o, status } : o)) })),
      clearAll: () => set({ orders: [] }),
    }),
    {
      name: "qrmenu-orders",
      storage: createJSONStorage(() => localStorage),
    }
  )
);
