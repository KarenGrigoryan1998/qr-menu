import React from 'react';
import toast from 'react-hot-toast';

/**
 * Toast notification utility
 * Provides consistent toast notifications across the app
 */

export const showToast = {
  /**
   * Success toast - green with checkmark
   */
  success: (message: string, duration = 3000) => {
    toast.success(message, {
      duration,
      style: {
        background: '#10b981',
        color: '#fff',
        padding: '16px',
        borderRadius: '12px',
        fontSize: '14px',
        fontWeight: '500',
      },
      iconTheme: {
        primary: '#fff',
        secondary: '#10b981',
      },
    });
  },

  /**
   * Persistent success toast with Close button
   */
  successPersistent: (message: string) => {
    const id = toast.custom((t) => (
      React.createElement(
        'div',
        { className: 'bg-emerald-600 text-white p-3 pr-2 rounded-xl shadow-lg flex items-start gap-3 max-w-sm' },
        React.createElement('span', { className: 'text-xl leading-none' }, '✓'),
        React.createElement('div', { className: 'flex-1 whitespace-pre-wrap text-sm font-medium' }, message),
        React.createElement(
          'button',
          {
            onClick: () => toast.remove(t.id),
            className: 'ml-2 inline-flex items-center justify-center w-7 h-7 rounded-md hover:bg-white/20 text-white/90',
            'aria-label': 'Close',
            title: 'Close',
          },
          '×'
        )
      )
    ), {
      duration: Infinity,
    });
    return id;
  },

  /**
   * Error toast - red with X
   */
  error: (message: string, duration = 4000) => {
    toast.error(message, {
      duration,
      style: {
        background: '#ef4444',
        color: '#fff',
        padding: '16px',
        borderRadius: '12px',
        fontSize: '14px',
        fontWeight: '500',
      },
      iconTheme: {
        primary: '#fff',
        secondary: '#ef4444',
      },
    });
  },

  /**
   * Info toast - blue with info icon
   */
  info: (message: string, duration = 3000) => {
    toast(message, {
      duration,
      icon: 'ℹ️',
      style: {
        background: '#3b82f6',
        color: '#fff',
        padding: '16px',
        borderRadius: '12px',
        fontSize: '14px',
        fontWeight: '500',
      },
    });
  },

  /**
   * Warning toast - yellow/orange
   */
  warning: (message: string, duration = 3500) => {
    toast(message, {
      duration,
      icon: '⚠️',
      style: {
        background: '#f59e0b',
        color: '#fff',
        padding: '16px',
        borderRadius: '12px',
        fontSize: '14px',
        fontWeight: '500',
      },
    });
  },

  /**
   * Loading toast - shows spinner
   */
  loading: (message: string) => {
    return toast.loading(message, {
      style: {
        background: '#6b7280',
        color: '#fff',
        padding: '16px',
        borderRadius: '12px',
        fontSize: '14px',
        fontWeight: '500',
      },
    });
  },

  /**
   * Promise toast - handles async operations
   */
  promise: <T,>(
    promise: Promise<T>,
    messages: {
      loading: string;
      success: string;
      error: string;
    }
  ) => {
    return toast.promise(
      promise,
      {
        loading: messages.loading,
        success: messages.success,
        error: messages.error,
      },
      {
        style: {
          padding: '16px',
          borderRadius: '12px',
          fontSize: '14px',
          fontWeight: '500',
        },
        success: {
          duration: 3000,
          style: {
            background: '#10b981',
            color: '#fff',
          },
        },
        error: {
          duration: 4000,
          style: {
            background: '#ef4444',
            color: '#fff',
          },
        },
      }
    );
  },

  /**
   * Custom toast with emoji
   */
  custom: (message: string, emoji: string, duration = 3000) => {
    toast(message, {
      duration,
      icon: emoji,
      style: {
        background: '#1f2937',
        color: '#fff',
        padding: '16px',
        borderRadius: '12px',
        fontSize: '14px',
        fontWeight: '500',
      },
    });
  },

  /**
   * Dismiss all toasts
   */
  dismiss: () => {
    toast.dismiss();
  },

  /**
   * Dismiss specific toast
   */
  dismissById: (id: string) => {
    toast.dismiss(id);
  },
  /**
   * Remove specific toast immediately (no animation)
   */
  removeById: (id: string) => {
    toast.remove(id);
  },
};
