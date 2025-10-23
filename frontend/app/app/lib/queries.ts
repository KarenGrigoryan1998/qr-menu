import { useQuery } from "@tanstack/react-query";
import { api } from "./api";

const RESTAURANT_ID = 1; // Demo Restaurant

export type Category = {
  id: number;
  name: string;
  name_hy: string;
  name_en: string;
  name_ru: string;
  image_url?: string | null;
};

export type Menu = {
  id: number;
  name: string;
  name_hy: string;
  name_en: string;
  name_ru: string;
  description?: string | null;
  description_hy?: string | null;
  description_en?: string | null;
  description_ru?: string | null;
  price: number;
  category_id: number;
  image_url?: string | null;
  available: boolean;
};

export type MenuResponse = {
  restaurant: {
    id: number;
    name: string;
    slug: string;
    settings: any;
  };
  categories: Array<{
    id: number;
    name_hy: string;
    name_en: string;
    name_ru: string;
    image_url?: string | null;
    items: Menu[];
  }>;
};

export function useMenuQuery(lang: "hy" | "en" | "ru" = "hy") {
  return useQuery({
    queryKey: ["menu", RESTAURANT_ID, lang],
    queryFn: async () => {
      const { data } = await api.get<MenuResponse>(`/api/restaurants/${RESTAURANT_ID}/menu`);
      return data;
    },
    staleTime: 5 * 60 * 1000,
  });
}

export function useCategoriesQuery(lang: "hy" | "en" | "ru" = "hy") {
  return useQuery({
    queryKey: ["categories", RESTAURANT_ID, lang],
    queryFn: async () => {
      const { data } = await api.get<Category[]>(`/api/restaurants/${RESTAURANT_ID}/categories`);
      // Map to include localized name
      return data.map(cat => ({
        ...cat,
        name: cat[`name_${lang}` as keyof Category] as string || cat.name_hy,
      }));
    },
    staleTime: 5 * 60 * 1000,
  });
}

export function useMenusByCategoryQuery(categoryId?: number, lang: "hy" | "en" | "ru" = "hy") {
  return useQuery({
    enabled: !!categoryId,
    queryKey: ["menus", RESTAURANT_ID, categoryId, lang],
    queryFn: async () => {
      const { data } = await api.get<Menu[]>(`/api/restaurants/${RESTAURANT_ID}/categories/${categoryId}/items`);
      // Map to include localized name and description
      return data.map(item => ({
        ...item,
        name: item[`name_${lang}` as keyof Menu] as string || item.name_hy,
        description: item[`description_${lang}` as keyof Menu] as string || item.description_hy,
      }));
    },
  });
}
