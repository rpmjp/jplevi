import { SiteHeader } from "@/components/SiteHeader";
import { SiteFooter } from "@/components/SiteFooter";
import { HudBackdrop } from "@/components/HudBackdrop";

/** Corporate chrome: parent home and division landings. */
export default function CorporateLayout({ children }: { children: React.ReactNode }) {
  return (
    <>
      <HudBackdrop />
      {/* No background here — HudBackdrop sits behind at -z-10. */}
      <div className="flex min-h-screen flex-col">
        <SiteHeader />
        <main id="main" className="flex-1">
          {children}
        </main>
        <SiteFooter />
      </div>
    </>
  );
}
