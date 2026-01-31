/** @type {import('tailwindcss').Config} */
module.exports = {
  /**
   * Content paths for purging unused CSS
   * CRITICAL: All PHP templates and JS files containing Tailwind classes
   */
  content: [
    './*.php',
    './templates/**/*.php',
    './page-templates/**/*.php',
    './patterns/**/*.php',
    './parts/**/*.html',
    './inc/blocks/src/**/*.{js,jsx,php}',
    './assets/js/src/**/*.js',
  ],

  /**
   * Dark mode strategy
   * 'class' allows manual toggle via .dark class on html/body
   */
  // darkMode: 'class',

  /**
   * RTL Support
   * Enable logical properties for bidirectional layouts
   */
  corePlugins: {
    // Disable default margin/padding, use logical properties
    // margin: false,
    // padding: false,
  },

  theme: {
    /**
     * Container configuration
     */
    container: {
      center: true,
      padding: {
        DEFAULT: '1rem',
        sm: '1.5rem',
        lg: '2rem',
        xl: '2.5rem',
      },
      screens: {
        sm: '640px',
        md: '768px',
        lg: '1024px',
        xl: '1280px',
        '2xl': '1400px', // Max container width for media platform
      },
    },

    extend: {
      /**
       * FIGMA DESIGN TOKENS
       * ==================
       * Replace these values with your Figma design system tokens
       */
      colors: {
        // Primary brand color (Greenergy green)
        primary: {
          50: '#ECFDF5',
          100: '#D1FAE5',
          200: '#A7F3D0',
          300: '#6EE7B7',
          400: '#34D399',
          500: '#10B981', // DEFAULT
          600: '#059669',
          700: '#047857',
          800: '#065F46',
          900: '#064E3B',
          950: '#022C22',
          DEFAULT: '#10B981',
        },
        // Secondary (dark/neutral)
        secondary: {
          50: '#F9FAFB',
          100: '#F3F4F6',
          200: '#E5E7EB',
          300: '#D1D5DB',
          400: '#9CA3AF',
          500: '#6B7280',
          600: '#4B5563',
          700: '#374151',
          800: '#1F2937', // DEFAULT
          900: '#111827',
          950: '#030712',
          DEFAULT: '#1F2937',
        },
        // Accent color (for CTAs, highlights)
        accent: {
          DEFAULT: '#F59E0B',
          light: '#FCD34D',
          dark: '#D97706',
        },
        // Semantic colors
        success: '#10B981',
        warning: '#F59E0B',
        error: '#EF4444',
        info: '#3B82F6',
      },

      /**
       * Typography
       * Font families - use self-hosted WOFF2 only
       */
      fontFamily: {
        sans: ['DIN Next LT Arabic Medium', 'system-ui', '-apple-system', 'sans-serif'],
        arabic: ['DIN Next LT Arabic Medium', 'Tahoma', 'sans-serif'],
        heading: ['DIN Next LT Arabic Medium', 'system-ui', 'sans-serif'],
      },

      /**
       * Font sizes with line-height
       */
      fontSize: {
        'xs': ['0.75rem', { lineHeight: '1rem' }],
        'sm': ['0.875rem', { lineHeight: '1.25rem' }],
        'base': ['1rem', { lineHeight: '1.5rem' }],
        'lg': ['1.125rem', { lineHeight: '1.75rem' }],
        'xl': ['1.25rem', { lineHeight: '1.75rem' }],
        '2xl': ['1.5rem', { lineHeight: '2rem' }],
        '3xl': ['1.875rem', { lineHeight: '2.25rem' }],
        '4xl': ['2.25rem', { lineHeight: '2.5rem' }],
        '5xl': ['3rem', { lineHeight: '1.15' }],
        '6xl': ['3.75rem', { lineHeight: '1.1' }],
      },

      /**
       * Spacing scale (8px base grid)
       * Add custom spacing from Figma
       */
      spacing: {
        '18': '4.5rem',
        '88': '22rem',
        '100': '25rem',
        '120': '30rem',
      },

      /**
       * Border radius
       */
      borderRadius: {
        'xl': '1rem',
        '2xl': '1.5rem',
        '3xl': '2rem',
      },

      /**
       * Box shadows
       */
      boxShadow: {
        'card': '0 2px 8px -2px rgba(0, 0, 0, 0.1), 0 4px 12px -4px rgba(0, 0, 0, 0.1)',
        'card-hover': '0 8px 24px -8px rgba(0, 0, 0, 0.15), 0 12px 32px -12px rgba(0, 0, 0, 0.15)',
        'dropdown': '0 10px 40px -10px rgba(0, 0, 0, 0.2)',
      },

      /**
       * Transitions
       */
      transitionDuration: {
        '250': '250ms',
        '350': '350ms',
      },

      /**
       * Z-index scale
       */
      zIndex: {
        'dropdown': '1000',
        'sticky': '1020',
        'fixed': '1030',
        'modal-backdrop': '1040',
        'modal': '1050',
        'popover': '1060',
        'tooltip': '1070',
      },

      /**
       * Aspect ratios
       */
      aspectRatio: {
        'card': '16 / 10',
        'hero': '21 / 9',
        'square': '1 / 1',
      },

      /**
       * Animations
       */
      animation: {
        'fade-in': 'fadeIn 0.3s ease-out',
        'slide-up': 'slideUp 0.3s ease-out',
        'slide-down': 'slideDown 0.3s ease-out',
      },
      keyframes: {
        fadeIn: {
          '0%': { opacity: '0' },
          '100%': { opacity: '1' },
        },
        slideUp: {
          '0%': { opacity: '0', transform: 'translateY(10px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
        slideDown: {
          '0%': { opacity: '0', transform: 'translateY(-10px)' },
          '100%': { opacity: '1', transform: 'translateY(0)' },
        },
      },
    },
  },

  /**
   * Plugins
   */
  plugins: [
    // Typography plugin for prose content
    require('@tailwindcss/typography'),

    // Forms plugin for better form styling
    require('@tailwindcss/forms')({
      strategy: 'class', // Only apply when .form-input etc classes used
    }),

    // RTL support with logical properties
    require('tailwindcss-rtl'),
  ],

  /**
   * Safelist
   * Classes that should never be purged (dynamically added via PHP/JS)
   */
  safelist: [
    // Dynamic color classes
    'bg-primary',
    'bg-secondary',
    'text-primary',
    'text-secondary',
    // Responsive visibility
    'hidden',
    'block',
    'lg:hidden',
    'lg:block',
    // Animation states
    'animate-fade-in',
    'animate-slide-up',
  ],
};
