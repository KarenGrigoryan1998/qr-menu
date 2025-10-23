"use client";
import React, { createContext, useContext, useEffect, useMemo, useState } from "react";
import { dictionaries, type Dict, type Lang } from "./dictionaries";

interface I18nValue {
  lang: Lang;
  t: (key: keyof Dict extends string ? string : string) => string;
  setLang: (l: Lang) => void;
}

const I18nContext = createContext<I18nValue | undefined>(undefined);

function getInitialLang(): Lang {
  if (typeof window === "undefined") return "hy";
  const url = new URL(window.location.href);
  const q = url.searchParams.get("lang");
  if (q === "hy" || q === "en" || q === "ru") return q;
  const saved = window.localStorage.getItem("qrmenu.lang");
  if (saved === "hy" || saved === "en" || saved === "ru") return saved;
  return "hy";
}

export function I18nProvider({ children }: { children: React.ReactNode }) {
  const [lang, setLangState] = useState<Lang>(getInitialLang);

  useEffect(() => {
    try {
      window.localStorage.setItem("qrmenu.lang", lang);
    } catch {}
  }, [lang]);

  const t = useMemo(() => {
    const dict = dictionaries[lang];
    return (key: string) => dict[key] ?? key;
  }, [lang]);

  const setLang = (l: Lang) => setLangState(l);

  const value = useMemo(() => ({ lang, t, setLang }), [lang, t]);

  return <I18nContext.Provider value={value}>{children}</I18nContext.Provider>;
}

export function useI18n() {
  const ctx = useContext(I18nContext);
  if (!ctx) throw new Error("useI18n must be used within I18nProvider");
  return ctx;
}
