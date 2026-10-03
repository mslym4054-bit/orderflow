import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Tajawal', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                base: '#0B0F12',
                night: '#0B0F12',
                surface: '#151B20',
                surface2: '#1C242A',
                line: '#262E35',
                ink: '#E7EDEC',
                muted: '#8B98A0',
                neon: {
                    DEFAULT: '#39FFC2',
                    dim: '#1FCFA0',
                },
            },
        },
    },

    plugins: [forms],
};
