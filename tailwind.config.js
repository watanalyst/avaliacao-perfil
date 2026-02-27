import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
        './node_modules/@jagua/ui/**/*.vue',
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
                primary: {
                    DEFAULT: '#093F87',
                    hover: '#0B56B3',
                    light: '#1565C0',
                    50: '#EFF6FF',
                    100: '#DBEAFE',
                    200: '#BFDBFE',
                    300: '#93C5FD',
                    400: '#60A5FA',
                    500: '#093F87',
                    600: '#0B56B3',
                    700: '#093F87',
                    800: '#07306A',
                    900: '#052350',
                },
                navy: {
                    DEFAULT: '#0A1E44',
                    light: '#12336B',
                    dark: '#071631',
                    darkest: '#082040',
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.5s ease-out',
                'slide-up': 'slideUp 0.4s ease-out',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0' },
                    '100%': { opacity: '1' },
                },
                slideUp: {
                    '0%': { opacity: '0', transform: 'translateY(16px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
        },
    },

    plugins: [forms],
};
