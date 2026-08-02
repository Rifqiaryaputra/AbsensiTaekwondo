import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class', // Mengaktifkan dark mode berbasis class
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],
    theme: {
        extend: {
            colors: {
                brand: {
                    blue: "#3554d1",
                    hover: "#2841a8",
                    light: "#eef2ff",
                },
                status: {
                    hadir: { bg: "#edfcf4", text: "#16a34a" },
                    izin: { bg: "#eff6ff", text: "#3b82f6" },
                    sakit: { bg: "#fefce8", text: "#eab308" },
                    alfa: { bg: "#fef2f2", text: "#ef4444" },
                    pending: { bg: "#fff7ed", text: "#d97706" }
                },
                dark: {
                    bg: "#111827",
                    card: "#1f2937",
                    border: "#374151",
                    text: "#f3f4f6",
                    muted: "#9ca3af"
                }
            },
            boxShadow: {
                'soft': '0 10px 40px -10px rgba(0,0,0,0.05)',
                'card': '0 4px 20px -4px rgba(0,0,0,0.03)',
            },
            fontFamily: {
                sans: ['Inter', ...defaultTheme.fontFamily.sans],
                heading: ['Poppins', ...defaultTheme.fontFamily.sans],
            }
        },
    },
    plugins: [forms],
};