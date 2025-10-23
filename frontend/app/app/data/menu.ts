export type MenuCategory = {
  id: string;
  name: string; // Display name (HY)
  nameEn?: string;
  nameRu?: string;
  image?: string; // representative image for the category card
};

export type MenuItem = {
  id: string;
  categoryId: string;
  name: string; // HY
  nameEn?: string;
  nameRu?: string;
  description?: string; // HY
  price: number; // AMD
  image?: string;
  available?: boolean;
  options?: Array<{
    id: string;
    name: string; // e.g., "Spice Level"
    choices: Array<{ id: string; label: string; priceDelta?: number }>;
    required?: boolean;
    maxChoices?: number; // default 1
  }>;
};

export const categories: MenuCategory[] = [
  {
    id: "grill",
    name: "Խորոված",
    nameEn: "Grill",
    image:
      "https://images.unsplash.com/photo-1550317138-10000687a72b?q=80&w=1400&auto=format&fit=crop", // skewers/grill
  },
  {
    id: "salad",
    name: "Աղցաններ",
    nameEn: "Salads",
    image:
      "https://images.unsplash.com/photo-1551183053-bf91a1d81141?q=80&w=1400&auto=format&fit=crop", // green salad
  },
  {
    id: "soup",
    name: "Ապուրներ",
    nameEn: "Soups",
    image:
      "https://images.unsplash.com/photo-1547592180-85f173990554?q=80&w=1400&auto=format&fit=crop", // soup bowl
  },
  {
    id: "drink",
    name: "Խմիչքներ",
    nameEn: "Drinks",
    image:
      "https://images.unsplash.com/photo-1510626176961-4b57d4fbad03?q=80&w=1400&auto=format&fit=crop", // cocktails/beverages
  },
];

export const items: MenuItem[] = [
  {
    id: "khorovats-mixed",
    categoryId: "grill",
    name: "Խորոված միքս",
    nameEn: "Mixed Khorovats",
    description: "Խոզ, հավ, գառ - մատուցվում է լավաշով և թթուկով",
    price: 8500,
    image: "https://images.unsplash.com/photo-1612874742237-6526221588e3?q=80&w=1200&auto=format&fit=crop",
    available: true,
    options: [
      {
        id: "spice",
        name: "Սրություն",
        choices: [
          { id: "mild", label: "Թեթև" },
          { id: "medium", label: "Միջին" },
          { id: "hot", label: "Սուր" },
        ],
        required: false,
        maxChoices: 1,
      },
    ],
  },
  {
    id: "tabbouleh",
    categoryId: "salad",
    name: "Թաբբուլե",
    nameEn: "Tabbouleh",
    description: "Թարմ, թեթև և բուրավետ աղցան",
    price: 3200,
    image: "https://images.unsplash.com/photo-1568605114967-8130f3a36994?q=80&w=1200&auto=format&fit=crop",
    available: true,
  },
  {
    id: "spas",
    categoryId: "soup",
    name: "Սպաս",
    nameEn: "Spas",
    description: "Ավանդական հայկական տաք մածնապուր",
    price: 2500,
    image: "https://images.unsplash.com/photo-1505575967455-40e256f73376?q=80&w=1200&auto=format&fit=crop",
    available: true,
  },
  {
    id: "tan",
    categoryId: "drink",
    name: "Թան",
    nameEn: "Tan",
    price: 600,
    image: "https://images.unsplash.com/photo-1526318472351-c75fcf070305?q=80&w=1200&auto=format&fit=crop",
    available: true,
  },
];