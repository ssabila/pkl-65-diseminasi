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
        extend: {}
    },
    plugins: [forms]
}
