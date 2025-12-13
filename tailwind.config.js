/** @type {import('tailwindcss').Config} */
module.exports = {
  content: [
    './app/Views/**/*.php',
    './app/Controllers/**/*.php',
    './public/**/*.html',
    './public/**/*.js',
  ],
  theme: {
    extend: {
      colors: {
        'primary-dark': '#2c3e50',
        'accent-gold': '#b89a66',
        'text-dark': '#3e5060',
        'bg-light': '#F7F8F9',
      },
    },
  },
  plugins: [],
}

