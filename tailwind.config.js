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
                sans:   ['Inter', ...defaultTheme.fontFamily.sans],
                outfit: ['Outfit', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                brand: {
                    50:  '#eff6ff',
                    100: '#dbeafe',
                    200: '#bfdbfe',
                    300: '#93c5fd',
                    400: '#60a5fa',
                    500: '#3b82f6',
                    600: '#2563eb',
                    700: '#1d4ed8',
                    800: '#1e40af',
                    900: '#1e3a8a',
                    950: '#172554',
                },
                surface: {
                    DEFAULT: '#ffffff',
                    dark:    '#0D1220',
                    card:    '#f8fafc',
                    'card-dark': '#141c2e',
                },
            },
            backgroundImage: {
                'gradient-brand':   'linear-gradient(135deg, #2563eb 0%, #1d4ed8 100%)',
                'gradient-surface': 'linear-gradient(180deg, rgba(37,99,235,0.05) 0%, transparent 100%)',
            },
            boxShadow: {
                'brand-sm': '0 2px 8px rgba(37, 99, 235, 0.25)',
                'brand':    '0 4px 20px rgba(37, 99, 235, 0.35)',
                'brand-lg': '0 8px 40px rgba(37, 99, 235, 0.45)',
                'card':     '0 1px 3px rgba(0,0,0,0.06), 0 1px 2px rgba(0,0,0,0.04)',
                'card-md':  '0 4px 16px rgba(0,0,0,0.08)',
                'card-lg':  '0 10px 40px rgba(0,0,0,0.12)',
            },
            animation: {
                'fade-in':       'fadeIn 0.25s ease both',
                'fade-in-up':    'fadeInUp 0.3s ease both',
                'fade-in-down':  'fadeInDown 0.3s ease both',
                'slide-in-left': 'slideInLeft 0.25s ease both',
            },
            keyframes: {
                fadeIn:      { from: { opacity: '0' },                               to: { opacity: '1' } },
                fadeInUp:    { from: { opacity: '0', transform: 'translateY(8px)' },  to: { opacity: '1', transform: 'translateY(0)' } },
                fadeInDown:  { from: { opacity: '0', transform: 'translateY(-8px)'},  to: { opacity: '1', transform: 'translateY(0)' } },
                slideInLeft: { from: { opacity: '0', transform: 'translateX(-12px)'}, to: { opacity: '1', transform: 'translateX(0)' } },
            },
            transitionDuration: {
                '200': '200ms',
            },
            borderRadius: {
                'xl':  '0.625rem',
                '2xl': '0.75rem',
                '3xl': '1rem',
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
