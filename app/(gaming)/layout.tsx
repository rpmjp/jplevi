import { SiteHeader } from "@/components/SiteHeader";
import { SiteFooter } from "@/components/SiteFooter";
import { HudBackdrop } from "@/components/HudBackdrop";

/**
 * Gaming-division chrome. Deliberately unchanged from the original cockpit-HUD
 * system so the games side reads as a separate property from JP Levi AI.
 */
export default function GamingLayout({ children }: { children: React.ReactNode }) {
  return (
    <>
      <HudBackdrop />
      {/* No background here - HudBackdrop sits behind at -z-10. */}
      <div className="hud-scope flex min-h-screen flex-col">
        <SiteHeader />
        <main id="main" className="flex-1">
          {children}
        </main>
        <SiteFooter />
      </div>
    </>
  );
}
