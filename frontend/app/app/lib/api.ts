import axios from "axios";

const baseURL = process.env.NEXT_PUBLIC_API_BASE || "http://localhost:8080";

export const api = axios.create({
  baseURL,
  headers: { "Content-Type": "application/json" },
});

// Optionally attach interceptors for auth/multi-tenant headers later
// api.interceptors.request.use((config) => {
//   // e.g., config.headers["X-Restaurant-ID"] = currentRestaurantId
//   return config;
// });
