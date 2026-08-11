import type { Metadata, Viewport } from "next";
import { MechaHeader } from "@/components/mechablast/MechaHeader";
import { MechaFooter } from "@/components/mechablast/MechaFooter";
import { game } from "./game";

export const metadata: Metadata = {
  // Section-wide title template; each page supplies its own `title`.
  title: {
    default: `${game.name}: ${game.pitch}`,
    template: `%s | ${game.name}`,
  },
};

export const viewport: Viewport = {
  themeColor: "#05070A",
  colorScheme: "dark",
};

/**
 * MechaBlast owns its chrome: the corporate header/footer live in
 * app/(corporate)/layout.tsx and deliberately do not apply here.
 */
export default function MechaBlastLayout({ children }: { children: React.ReactNode }) {
  return (
    <div className="mecha-scope flex min-h-screen flex-col bg-mecha-void text-ink">
      <MechaHeader />
      <main id="main" className="flex-1">
        {children}
      </main>
      <MechaFooter />
    </div>
  );
}
