/** @type {import('tailwindcss').Config} */
module.exports = {
  darkMode: 'class',
  content: [
    "./components/**/*.{js,vue,ts}",
    "./layouts/**/*.vue",
    "./pages/**/*.vue",
    "./plugins/**/*.{js,ts}",
    "./app.vue",
  ],
  theme: {
    extend: {
      colors: {
        // Couleurs principales du service public togolais
        primary: {
          50: '#e6f4f1',
          100: '#b3e0d6',
          200: '#80cbb9',
          300: '#4db69d',
          400: '#26a788',
          500: '#009874', // Vert principal (couleur de base)
          600: '#008a69',
          700: '#00785c',
          800: '#00664f',
          900: '#004a39',
        },
        secondary: {
          50: '#fff9e6',
          100: '#ffecb3',
          200: '#ffdf80',
          300: '#ffd24d',
          400: '#ffc926',
          500: '#ffc107', // Jaune/doré
          600: '#ffb300',
          700: '#ffa000',
          800: '#ff8f00',
          900: '#ff6f00',
        },
        dark: {
          50: '#f5f6f7',
          100: '#e4e7eb',
          200: '#cbd2d9',
          300: '#9aa5b1',
          400: '#7b8794',
          500: '#616e7c',
          600: '#52606d',
          700: '#3e4c59',
          800: '#323f4b',
          900: '#1f2933', // Fond sombre principal
        }
      },
      backgroundColor: {
        'primary-dark': '#004a39',
        'secondary-dark': '#1f2933',
      }
    },
  },
  plugins: [],
}
