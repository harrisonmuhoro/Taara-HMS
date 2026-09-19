import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',

    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.ts',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans:    ['"Source Sans 3"', ...defaultTheme.fontFamily.sans],
                display: ['"Source Serif 4"', ...defaultTheme.fontFamily.serif],
            },
            colors: {
                brand: {
                    50:  '#FBF3EC',
                    100: '#F4E0D1',
                    200: '#E8C3A8',
                    300: '#D99A70',
                    400: '#C97446',
                    500: '#B85A2A',
                    600: '#9E4520',
                    700: '#7F371B',
                    800: '#5C2816',
                    900: '#3D1C10',
                    950: '#24110A',
                },
                olive: {
                    50:  '#F1F3EC',
                    100: '#E0E6D4',
                    200: '#C5D0AE',
                    300: '#A3B382',
                    400: '#7A8F58',
                    500: '#5A6B45',
                    600: '#4A5C3A',
                    700: '#3A482E',
                    800: '#2B3522',
                    900: '#1C2316',
                },
                paper: {
                    DEFAULT: '#F4EEE4',
                    dark:    '#1A1612',
                },
                surface: {
                    DEFAULT: '#FFFBF4',
                    dark:    '#241E18',
                    card:    '#FFFBF4',
                    'card-dark': '#241E18',
                },
                ink: {
                    DEFAULT: '#2C241C',
                    muted:   '#6E6458',
                },
            },
            boxShadow: {
                'card':    'none',
                'card-md': 'none',
                'card-lg': 'none',
            },
            transitionDuration: {
                '200': '200ms',
            },
            keyframes: {
                'fade-in-up': {
                    '0%': { opacity: '0', transform: 'translateY(0.5rem)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
            },
            animation: {
                'fade-in-up': 'fade-in-up 300ms ease-out both',
            },
            borderRadius: {
                'xl':  '0.375rem',
                '2xl': '0.375rem',
                '3xl': '0.5rem',
            },
            spacing: {
                '18': '4.5rem',
                '22': '5.5rem',
                '72': '18rem',
                '84': '21rem',
                '96': '24rem',
            },
        },
    },

    plugins: [forms],
};
