/** @type {import('tailwindcss').Config} */
export default {
    content: [
        "./resources/**/*.blade.php",
        "./resources/**/*.js",
        "./resources/**/*.vue",
    ],
    theme: {
        extend: {
            fontFamily: {
                sans: ["Inter", "sans-serif"],
            },
            colors: {
                brand: {
                    blue: {
                        DEFAULT: "#228bcc",
                        50: "#e8f4fb",
                        100: "#d1e9f7",
                        200: "#a3d3ef",
                        300: "#75bde7",
                        400: "#47a7df",
                        500: "#228bcc",
                        600: "#1b6fa3",
                        700: "#14537a",
                        800: "#0d3751",
                        900: "#061b28",
                    },
                    black: "#000000",
                    gray: "#6b7280",
                    light: "#f5f7fa",
                },
            },
        },
    },
    plugins: [],
};
