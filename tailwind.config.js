/** @type {import('tailwindcss').Config} */
export default {
  content: [
    "./index.html",
    "./src/**/*.{js,ts,jsx,tsx}",
  ],
  darkMode: 'class',
  theme: {
    extend: {
      colors: {
        hub: {
          950: '#07090e',
          900: '#0b0f19',
          850: '#111726',
          800: '#161f33',
          750: '#1e2942',
          700: '#253454',
          600: '#384c78',
        },
        meta: '#0668E1',
        google: '#EA4335',
        tiktok: '#FE2C55',
        linkedin: '#0A66C2',
        kwai: '#FF5000',
        brand: {
          cyan: '#00F2FE',
          violet: '#7928CA',
          emerald: '#10B981',
          rose: '#F43F5E',
          amber: '#F59E0B',
          indigo: '#6366F1'
        }
      },
      fontFamily: {
        sans: ['Plus Jakarta Sans', 'Inter', 'sans-serif'],
      },
      backgroundImage: {
        'gradient-radial': 'radial-gradient(var(--tw-gradient-stops))',
        'glass-card': 'linear-gradient(135deg, rgba(255, 255, 255, 0.05) 0%, rgba(255, 255, 255, 0.01) 100%)',
        'glass-hover': 'linear-gradient(135deg, rgba(255, 255, 255, 0.09) 0%, rgba(255, 255, 255, 0.03) 100%)',
        'neon-glow': 'radial-gradient(circle, rgba(0,242,254,0.15) 0%, rgba(121,40,202,0.05) 70%, transparent 100%)',
      },
      boxShadow: {
        'glass': '0 8px 32px 0 rgba(0, 0, 0, 0.37)',
        'neon-blue': '0 0 20px rgba(0, 242, 254, 0.25)',
        'neon-violet': '0 0 20px rgba(121, 40, 202, 0.25)',
      },
      backdropBlur: {
        xs: '2px',
      }
    },
  },
  plugins: [],
}
