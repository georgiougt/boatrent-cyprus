/**
 * Tailwind config — mirrors the old CDN Play config so the compiled
 * stylesheet is a drop-in replacement. Build with `npm run build:css`;
 * the minified output (css/tailwind.css) is committed and served directly
 * in production (no Node/build step needed on the server).
 */
module.exports = {
  content: [
    './*.php',
    './includes/**/*.php',
    './admin/**/*.php',
    './js/**/*.js',
  ],
  // Classes toggled at runtime in js/main.js — never present as literals in
  // the initial HTML, so pin them here so JIT never purges them.
  safelist: [
    'hidden',
    'opacity-0',
    'pointer-events-none',
    'ring-1',
    'ring-2',
    'ring-brand-gold',
    'border-red-500',
    'ring-red-500',
    'is-visible',
    'open',
    'scrolled',
  ],
  theme: {
    extend: {
      colors: {
        brand: {
          ink:   '#0D1A33',
          navy:  '#16395A',
          navy2: '#1F567C',
          gold:  '#12A4C9',
          goldL: '#46C2E0',
          aqua:  '#1ECEB6',
          aquaD: '#0C7A6E',
          sand:  '#E7F7FA',
          foam:  '#D2E7EF',
        },
      },
      fontFamily: {
        display: ['"Playfair Display"', 'serif'],
        body: ['"Plus Jakarta Sans"', 'sans-serif'],
      },
      keyframes: {
        fadeUp: { '0%': { opacity: 0, transform: 'translateY(34px)' }, '100%': { opacity: 1, transform: 'translateY(0)' } },
        floaty: { '0%,100%': { transform: 'translateY(0)' }, '50%': { transform: 'translateY(-10px)' } },
      },
      animation: {
        'fade-up': 'fadeUp 0.7s ease-out forwards',
        floaty: 'floaty 6s ease-in-out infinite',
      },
    },
  },
  plugins: [],
};
