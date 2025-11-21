import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

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
                // Font Body (TT Bells)
                sans: ['"TT Bells"', ...defaultTheme.fontFamily.sans],
                // Font Judul Besar (Rakkas)
                headline: ['"Rakkas"', 'serif'],
                // Font Menu & Sub-judul (Yodnam)
                sub: ['"Yodnam"', 'sans-serif'],
            },
            colors: {
                'pkl-base-orange': '#F58220',  
                'pkl-base-cream': '#FDF8E1',   
                'pkl-compliment-blue': '#3A539B',
                'pkl-compliment-yellow': '#FBE18B',
                'pkl-dark-blue': '#1E3A8A',
            }
        },
    },

    plugins: [forms],
};