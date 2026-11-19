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
        './resources/js/**/*.ts',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Inter', 'Figtree', ...defaultTheme.fontFamily.sans],
                mono: ['JetBrains Mono', ...defaultTheme.fontFamily.mono],
            },
            colors: {
                primary: {
                    50: '#eef2f6',
                    100: '#dce5ee',
                    200: '#b8c9d9',
                    300: '#8aa3bd',
                    400: '#5c7a9a',
                    500: '#3d5a78',
                    600: '#18283a',
                    700: '#152433',
                    800: '#121e2c',
                    900: '#0f1924',
                    950: '#0c141c',
                    DEFAULT: '#18283a',
                },
            },
        },
    },

    plugins: [forms],
};
