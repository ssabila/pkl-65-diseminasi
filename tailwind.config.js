import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: false, // Dark mode disabled

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['"TT Bells"', ...defaultTheme.fontFamily.sans],
                headline: ['"Rakkas"', 'serif'],
                sub: ['"Yodnam"', 'sans-serif'],
            },
            colors: {
                // --- KEMBALI KE NAMA VARIABEL ASLI (DENGAN WARNA BARU) ---
                'pkl-base-orange': '#ef874b',  // Hex Baru
                'pkl-base-cream': '#fffbdf',   // Hex Baru
                
                // Warna Pelengkap (Sesuai Request)
                'pkl-compliment-yellow': '#fcda7b',
                'pkl-compliment-light-orange': '#f69a5c',
                'pkl-compliment-teal': '#50829b',
                'pkl-compliment-green': '#748d63',
                'pkl-compliment-purple': '#8174a0',

                // Warna Netral (Agar teks terbaca)
                'pkl-dark': '#333333',
                'pkl-text': '#555555',
                'pkl-border': '#e5e5e5',
            }
        },
    },

    plugins: [forms],
};