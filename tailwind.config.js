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
      animation: {
        'bounce-slow': 'bounce-slow 2s ease-in-out infinite',
        'ping-once': 'ping-once 1s cubic-bezier(0, 0, 0.2, 1)',
        'ping-subtle': 'ping-subtle 3s ease-in-out infinite',
      },
      keyframes: {
        'bounce-slow': {
          '0%, 100%': { 
            transform: 'translateY(0)',
            animationTimingFunction: 'cubic-bezier(0.8, 0, 1, 1)',
          },
          '50%': { 
            transform: 'translateY(-8px)',
            animationTimingFunction: 'cubic-bezier(0, 0, 0.2, 1)',
          },
        },
        'ping-once': {
          '0%': { 
            transform: 'scale(1)',
            opacity: '1',
          },
          '75%, 100%': { 
            transform: 'scale(1.5)',
            opacity: '0',
          },
        },
        'ping-subtle': {
          '0%, 100%': { 
            transform: 'scale(1)',
            opacity: '0.5',
          },
          '50%': { 
            transform: 'scale(1.1)',
            opacity: '0',
          },
        },
      },
    },
  },
  plugins: [],
}

