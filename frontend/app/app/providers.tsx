"use client";
import React from "react";
import { I18nProvider } from "./i18n/I18nContext";
import { QueryClient, QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "react-hot-toast";

export function Providers({ children }: { children: React.ReactNode }) {
  const [client] = React.useState(() => new QueryClient());
  return (
    <QueryClientProvider client={client}>
      <I18nProvider>
        {children}
        <Toaster position="top-center" reverseOrder={false} />
      </I18nProvider>
    </QueryClientProvider>
  );
}
