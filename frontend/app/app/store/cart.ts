"use client";
import { create } from "zustand";
import { persist, createJSONStorage } from "zustand/middleware";

export type CartLine = {
  itemId: string;
  name: string;
  price: number;
  qty: number;
  comment?: string;
  // options: Record<string, string[]>; // for future modifiers
};

export type CartState = {
  tableId?: string; // binds the cart to a table
  restaurantId?: string; // restaurant context
  currentOrderId?: number; // track active order
  lines: CartLine[];
  setTable: (tableId: string, restaurantId?: string) => void;
  setCurrentOrder: (orderId: number) => void;
  addItem: (item: { id: string; name: string; price: number }) => void;
  setQty: (itemId: string, qty: number) => void;
  setComment: (itemId: string, comment: string) => void;
  removeItem: (itemId: string) => void;
  clear: () => void;
  clearOrder: () => void; // clear order but keep table
  subtotal: () => number;
  serviceFee: () => number; // simple 10%
  total: () => number;
};

export const useCartStore = create<CartState>()(
  persist(
    (set, get) => ({
      tableId: undefined,
      restaurantId: undefined,
      currentOrderId: undefined,
      lines: [],
      setTable: (tableId, restaurantId) => {
        const current = get().tableId;
        const currentRestaurant = get().restaurantId;

        // If switching to different table or restaurant, clear cart
        if ((current && current !== tableId) || (currentRestaurant && currentRestaurant !== restaurantId)) {
          set({ tableId, restaurantId, lines: [], currentOrderId: undefined });
        } else {
          // Always update tableId and restaurantId
          set({ tableId, restaurantId });
        }
      },
      setCurrentOrder: (orderId) => set({ currentOrderId: orderId }),
      addItem: (item) => {
        const itemId = item.id;
        const { lines } = get();
        const exists = lines.find((l) => l.itemId === itemId);
        if (exists) {
          set({
            lines: lines.map((l) => (l.itemId === itemId ? { ...l, qty: l.qty + 1 } : l)),
          });
        } else {
          set({
            lines: [
              ...lines,
              { itemId, name: item.name, price: item.price, qty: 1, comment: "" },
            ],
          });
        }
      },
      setQty: (itemId, qty) => {
        if (qty <= 0) {
          get().removeItem(itemId);
        } else {
          set({
            lines: get().lines.map((l) => (l.itemId === itemId ? { ...l, qty } : l)),
          });
        }
      },
      setComment: (itemId, comment) => {
        set({
          lines: get().lines.map((l) => (l.itemId === itemId ? { ...l, comment } : l)),
        });
      },
      removeItem: (itemId) => {
        set({ lines: get().lines.filter((l) => l.itemId !== itemId) });
      },
      clear: () => set({ lines: [], currentOrderId: undefined }),
      clearOrder: () => set({ lines: [] }),
      subtotal: () => get().lines.reduce((s, l) => s + l.price * l.qty, 0),
      serviceFee: () => Math.round(get().subtotal() * 0.1),
      total: () => get().subtotal() + get().serviceFee(),
    }),
    {
      name: "qrmenu-cart",
      storage: createJSONStorage(() => localStorage),
      partialize: (state) => ({
        tableId: state.tableId,
        restaurantId: state.restaurantId,
        currentOrderId: state.currentOrderId,
        lines: state.lines
      }),
    }
  )
);
