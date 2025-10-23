const API_BASE = process.env.NEXT_PUBLIC_API_URL || 'http://localhost:8080/api';

export type OrderItem = {
  menu_id: number;
  quantity: number;
  comment?: string;
};

export type Order = {
  id: number;
  restaurant_id: number;
  table_id: number;
  status: 'pending' | 'confirmed' | 'preparing' | 'ready' | 'served' | 'completed' | 'cancelled';
  payment_status: 'unpaid' | 'paid' | 'refunded';
  total_amount: number;
  items: Array<{
    id: number;
    menu_id: number;
    quantity: number;
    price: number;
    comment?: string;
    menu: {
      id: number;
      name: string;
      price: number;
      image_url?: string;
    };
  }>;
  table?: {
    id: number;
    number: number;
    status: string;
  };
  created_at: string;
  updated_at: string;
};

export type ApiResponse<T> = {
  success: boolean;
  message?: string;
  data?: T;
  errors?: Record<string, string[]>;
};

export const orderApi = {
  /**
   * Create a new order
   */
  async createOrder(
    restaurantId: string,
    tableId: string,
    items: OrderItem[]
  ): Promise<ApiResponse<Order>> {
    try {
      const response = await fetch(`${API_BASE}/restaurants/${restaurantId}/orders`, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({
          table_id: parseInt(tableId),
          items,
        }),
      });

      return await response.json();
    } catch (error) {
      console.error('Create order error:', error);
      return {
        success: false,
        message: 'Failed to create order. Please try again.',
      };
    }
  },

  /**
   * Add items to existing order
   */
  async addItems(
    restaurantId: string,
    orderId: number,
    items: OrderItem[]
  ): Promise<ApiResponse<Order>> {
    try {
      const response = await fetch(
        `${API_BASE}/restaurants/${restaurantId}/orders/${orderId}/items`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ items }),
        }
      );

      return await response.json();
    } catch (error) {
      console.error('Add items error:', error);
      return {
        success: false,
        message: 'Failed to add items. Please try again.',
      };
    }
  },

  /**
   * Get order details
   */
  async getOrder(restaurantId: string, orderId: number): Promise<ApiResponse<Order>> {
    try {
      const response = await fetch(
        `${API_BASE}/restaurants/${restaurantId}/orders/${orderId}`
      );

      return await response.json();
    } catch (error) {
      console.error('Get order error:', error);
      return {
        success: false,
        message: 'Failed to fetch order details.',
      };
    }
  },

  /**
   * Get active orders for a table
   */
  async getTableOrders(
    restaurantId: string,
    tableId: string
  ): Promise<ApiResponse<Order[]>> {
    try {
      const url = `${API_BASE}/restaurants/${restaurantId}/tables/${tableId}/orders`;
      console.log('Fetching orders from:', url);
      
      const response = await fetch(url);
      console.log('Response status:', response.status);
      
      const data = await response.json();
      console.log('Response data:', data);
      
      return data;
    } catch (error) {
      console.error('Get table orders error:', error);
      return {
        success: false,
        message: 'Failed to fetch orders.',
      };
    }
  },

  /**
   * Request waiter assistance for an order
   */
  async requestWaiter(
    restaurantId: string,
    orderId: number
  ): Promise<ApiResponse<Order>> {
    try {
      const url = `${API_BASE}/restaurants/${restaurantId}/orders/${orderId}/request-waiter`;
      console.log('Requesting waiter from:', url);
      
      const response = await fetch(url, {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
      });
      
      console.log('Request waiter response status:', response.status);
      const data = await response.json();
      console.log('Request waiter response data:', data);

      return data;
    } catch (error) {
      console.error('Request waiter error:', error);
      return {
        success: false,
        message: 'Failed to request waiter.',
      };
    }
  },

  /**
   * Process payment for an order
   */
  async processPayment(
    restaurantId: string,
    orderId: number,
    paymentMethod: string,
    transactionId?: string
  ): Promise<ApiResponse<Order>> {
    try {
      const response = await fetch(
        `${API_BASE}/restaurants/${restaurantId}/orders/${orderId}/payment`,
        {
          method: 'POST',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({
            payment_method: paymentMethod,
            transaction_id: transactionId,
          }),
        }
      );

      return await response.json();
    } catch (error) {
      console.error('Process payment error:', error);
      return {
        success: false,
        message: 'Payment processing failed.',
      };
    }
  },

  /**
   * Update order status (admin/waiter use)
   */
  async updateStatus(
    restaurantId: string,
    orderId: number,
    status: string
  ): Promise<ApiResponse<Order>> {
    try {
      const response = await fetch(
        `${API_BASE}/restaurants/${restaurantId}/orders/${orderId}/status`,
        {
          method: 'PATCH',
          headers: { 'Content-Type': 'application/json' },
          body: JSON.stringify({ status }),
        }
      );

      return await response.json();
    } catch (error) {
      console.error('Update status error:', error);
      return {
        success: false,
        message: 'Failed to update status.',
      };
    }
  },
};
