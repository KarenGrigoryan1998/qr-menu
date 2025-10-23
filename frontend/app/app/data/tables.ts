export type Table = {
  id: number;
  token?: string; // optional short token for QR deep-link validation
  name?: string; // e.g., "Table 12"
};

export const tables: Table[] = Array.from({ length: 20 }).map((_, i) => ({
  id: i + 1,
  name: `Սեղան ${i + 1}`,
  token: undefined,
}));
