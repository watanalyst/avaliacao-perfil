import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50:  '#E8F0FA',
                    100: '#C5D9F1',
                    200: '#9CBDE5',
                    300: '#6E9BD6',
                    400: '#4A7EC8',
                    500: '#0B5AB5',
                    600: '#093F87',
                    700: '#073068',
                    800: '#052149',
                    900: '#03132B',
                },
            },
        },
    },

    plugins: [forms],
};
