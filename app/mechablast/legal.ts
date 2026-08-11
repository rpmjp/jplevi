import { readFileSync } from "node:fs";
import path from "node:path";

/**
 * Reads a legal document from content/mechablast/ at build time.
 *
 * This runs during `next build` only — under output: "export" every page is
 * prerendered, so there is no request-time filesystem access.
 */
export type LegalSlug = "privacy" | "terms";

export function readLegalDocument(slug: LegalSlug): string {
  const file = path.join(process.cwd(), "content", "mechablast", `${slug}.md`);
  return readFileSync(file, "utf8");
}
