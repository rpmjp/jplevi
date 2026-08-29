/**
 * Hero artwork. The annotation callouts, readout chips, and coordinate ticks are
 * part of the image itself, so the page does not repeat them in markup.
 *
 * The artwork carries a real alpha channel, so it composites straight onto the
 * paper ground with no blend mode.
 */
export function HeroArt() {
  return (
    <picture>
      <source srcSet="/hero.webp" type="image/webp" />
      {/* eslint-disable-next-line @next/next/no-img-element -- images.unoptimized is on; static export ships plain <img>. */}
      <img
        src="/hero.png"
        alt="Documents and data planes folding into a blue embedding structure, threaded by a knowledge graph. Three stages are annotated: Retrieval, find the right context across your data; Reasoning, infer, synthesize, and generate with precision; Deployment, ship, monitor, and operate with confidence."
        width={1536}
        height={1024}
        decoding="async"
        fetchPriority="high"
        className="h-auto w-full"
      />
    </picture>
  );
}
