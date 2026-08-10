import type { ReactNode } from "react";

/**
 * Corner-bracket HUD panel. Renders all four brackets: two via ::before/::after
 * in CSS, two as spans so no wrapper element is wasted.
 */
export function Panel({
  as: Tag = "div",
  className = "",
  children,
}: {
  as?: "div" | "section" | "article" | "li";
  className?: string;
  children: ReactNode;
}) {
  return (
    <Tag className={`panel panel-brackets ${className}`}>
      <span className="bracket-tr" aria-hidden="true" />
      <span className="bracket-bl" aria-hidden="true" />
      {children}
    </Tag>
  );
}
