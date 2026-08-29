/**
 * The JP Levi AI mark. Same drawing as app/icon.svg, minus the white plate:
 * the favicon needs one to read against dark tab chrome, the header does not.
 */
export function BizMark({ className = "" }: { className?: string }) {
  return (
    <svg viewBox="0 0 36 36" aria-hidden="true" className={className}>
      <path
        d="M6 20 L18 26.5 L18 6 L30 7.7 L30 18.5 L23.4 17.6 L23.4 26.5"
        fill="none"
        stroke="#1877F2"
        strokeWidth="2.4"
        strokeLinecap="square"
        strokeLinejoin="miter"
      />
    </svg>
  );
}
