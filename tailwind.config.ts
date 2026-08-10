import type { Config } from "tailwindcss";

/**
 * Cockpit-HUD design tokens.
 * Gunmetal base + one signal-orange accent (CTAs/energy), teal for HUD/telemetry
 * lines, amber reserved strictly for hazard/attention states.
 */
const config: Config = {
  content: [
    "./app/**/*.{ts,tsx}",
    "./components/**/*.{ts,tsx}",
  ],
  theme: {
    extend: {
      colors: {
        gun: {
          900: "#0B0F13", // deepest hull
          800: "#10161C", // page base
          700: "#151D25", // panel
          600: "#1C2630", // raised panel
          500: "#26333F", // hairline / border
          400: "#37485A", // strong border
        },
        ink: {
          DEFAULT: "#DCE4EC", // primary text
          muted: "#93A2B1", // secondary text
          dim: "#64748B", // tertiary / labels
        },
        signal: {
          DEFAULT: "#FF6A1F", // signal orange — CTAs, energy
          soft: "#FF8A4C",
          deep: "#C74A0E",
        },
        hud: {
          DEFAULT: "#3ECFC6", // teal — telemetry, brackets, readouts
          soft: "#7FE3DC",
          deep: "#1D8A84",
        },
        hazard: {
          DEFAULT: "#F5B33C", // amber — attention/hazard only
          deep: "#B87E15",
        },
      },
      fontFamily: {
        display: ["var(--font-display)", "ui-sans-serif", "system-ui", "sans-serif"],
        sans: ["var(--font-body)", "ui-sans-serif", "system-ui", "sans-serif"],
        mono: ["var(--font-mono)", "ui-monospace", "SFMono-Regular", "monospace"],
      },
      letterSpacing: {
        hud: "0.22em",
        wordmark: "0.06em",
      },
      maxWidth: {
        shell: "78rem",
      },
      boxShadow: {
        panel: "0 1px 0 0 rgba(255,255,255,0.03) inset, 0 24px 48px -32px rgba(0,0,0,0.9)",
        signal: "0 0 0 1px rgba(255,106,31,0.35), 0 12px 32px -16px rgba(255,106,31,0.55)",
      },
      backgroundImage: {
        grid:
          "linear-gradient(to right, rgba(62,207,198,0.055) 1px, transparent 1px), linear-gradient(to bottom, rgba(62,207,198,0.055) 1px, transparent 1px)",
      },
      backgroundSize: {
        // Distinct key so it doesn't collide with the `bg-grid` image utility.
        "grid-cell": "72px 72px",
      },
      keyframes: {
        scan: {
          "0%": { transform: "translateY(-100%)" },
          "100%": { transform: "translateY(100vh)" },
        },
        boot: {
          "0%": { opacity: "0", transform: "translateY(8px)" },
          "100%": { opacity: "1", transform: "translateY(0)" },
        },
        sweep: {
          "0%, 100%": { opacity: "0.25" },
          "50%": { opacity: "0.7" },
        },
      },
      animation: {
        scan: "scan 9s linear infinite",
        boot: "boot 700ms cubic-bezier(0.22, 1, 0.36, 1) both",
        sweep: "sweep 4s ease-in-out infinite",
      },
    },
  },
  plugins: [],
};

export default config;
