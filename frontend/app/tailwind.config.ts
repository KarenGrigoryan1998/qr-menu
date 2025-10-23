import type { Config } from "tailwindcss";

const config: Config = {
  content: [
    "./pages/**/*.{js,ts,jsx,tsx,mdx}",
    "./components/**/*.{js,ts,jsx,tsx,mdx}",
    "./app/**/*.{js,ts,jsx,tsx,mdx}",
  ],
  theme: {
    extend: {
      fontFamily: {
        sans: ["var(--font-sans)", "ui-sans-serif", "system-ui", "-apple-system", "Segoe UI", "Roboto", "Noto Sans", "Ubuntu", "Cantarell", "Helvetica Neue", "Arial", "sans-serif"],
        display: ["var(--font-display)", "ui-serif", "Georgia", "Cambria", "Times New Roman", "Times", "serif"],
      },
      colors: {
        brand: {
          DEFAULT: "#B2432F",
          50: "#FCE9E6",
          100: "#F7D6D1",
          200: "#EEAEA5",
          300: "#E48679",
          400: "#D95E4D",
          500: "#B2432F",
          600: "#8E3626",
          700: "#6A291D",
          800: "#451B13",
          900: "#210E0A",
        },
        surface: "#FFF7F0",
        text: "#1C1C1C",
        muted: "#7A7A7A",
        accent: "#2F6CB2",
      },
      backgroundImage: {
        "gradient-radial": "radial-gradient(var(--tw-gradient-stops))",
        "gradient-conic":
          "conic-gradient(from 180deg at 50% 50%, var(--tw-gradient-stops))",
      },
    },
  },
  plugins: [],
};
export default config;
