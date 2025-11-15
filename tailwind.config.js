/** @type {import('tailwindcss').Config} */
import forms from '@tailwindcss/forms'
import defaultTheme from 'tailwindcss/defaultTheme'

export default {
    darkMode: 'class',
    content: [
        './resources/**/*.blade.php',
        './resources/**/*.js',
        './resources/**/*.vue',
        './pages/**/*.{html,js,vue}',
        './components/**/*.{html,js,vue}'
    ],
    theme: {
        extend: {
            // --- KUSTOMISASI PKL 65 ---
            colors: {
                'pkl-base-orange': '#F58220',
                'pkl-base-cream': '#FDF8E1',

                'pkl-compliment-yellow': '#FBE18B',
                'pkl-compliment-blue': '#3A539B',
                'pkl-compliment-green': '#668A4B',
                'pkl-compliment-purple': '#6E498B'
            },

            fontFamily: {
                sans: ['"TT Bells"', 'sans-serif'],
                headline: ['Rakkas', 'serif'],
                sub: ['Yodnam', 'sans-serif']
            }
        }
    },
    plugins: [forms]
}
