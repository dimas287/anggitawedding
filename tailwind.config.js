/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'selector',
    content: [
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './resources/js/**/*.jsx',
    ],
    theme: {
        extend: {
            fontFamily: {
                playfair: ['"Playfair Display"', 'serif'],
                poppins: ['Poppins', 'sans-serif'],
                'space-grotesk': ['"Space Grotesk"', 'sans-serif'],
            },
            colors: {
                gold: {
                    DEFAULT: '#D4AF37',
                    light: '#F0D060',
                    dark: '#B8960C',
                },
                rose: {
                    wedding: '#E91E8C',
                },
            },
        },
    },
    plugins: [],
};
