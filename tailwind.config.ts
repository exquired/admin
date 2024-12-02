import type { Config } from 'tailwindcss';
import colors from 'tailwindcss/colors';

const config: Config = {
  content: ['./src/**/*.{html,js,svelte,ts}'],
  theme: {
    extend: {
      fontFamily: {
        'matiott': ['MATIOTT ELEGANT FONT', 'sans-serif'],
        'montserratt': ['MONTSERRAT', 'sans-serif'],
      },
      colors: {
        transparent: 'transparent',
        current: 'currentColor',
        black: colors.black,
        white: colors.white,
        gray: colors.gray, 
        indigo: colors.indigo,
        red: colors.rose,
        yellow: colors.amber,
        custom: '#607262',
      }
    },
  },
  plugins: [],
};

export default config;
