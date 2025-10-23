"use client";
import { useI18n } from "../i18n/I18nContext";

export function OrderStatusBadge({ status }: { status: string }) {
  const { t } = useI18n();

  const statusConfig: Record<
    string,
    { color: string; borderColor: string; icon: string }
  > = {
    pending: { 
      color: "bg-yellow-50 text-yellow-700 border-yellow-200", 
      borderColor: "border",
      icon: "⏳" 
    },
    paid: { 
      color: "bg-green-50 text-green-700 border-green-200", 
      borderColor: "border",
      icon: "💳" 
    },
    cooking: { 
      color: "bg-orange-50 text-orange-700 border-orange-200", 
      borderColor: "border",
      icon: "👨‍🍳" 
    },
    ready: { 
      color: "bg-emerald-50 text-emerald-700 border-emerald-200", 
      borderColor: "border",
      icon: "✓" 
    },
    delivered: { 
      color: "bg-purple-50 text-purple-700 border-purple-200", 
      borderColor: "border",
      icon: "🍽️" 
    },
    closed: { 
      color: "bg-slate-50 text-slate-700 border-slate-200", 
      borderColor: "border",
      icon: "🔒" 
    },
  };

  const config = statusConfig[status] || statusConfig.pending;

  return (
    <span
      className={`inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full text-xs font-semibold ${config.color} ${config.borderColor}`}
    >
      <span className="text-sm">{config.icon}</span>
      <span>{t(status)}</span>
    </span>
  );
}
