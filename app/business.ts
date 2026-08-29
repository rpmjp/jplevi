/**
 * JP Levi AI - the business side of the site.
 * Everything the cream/ink pages render comes from here.
 * The gaming side (app/(gaming)/ and app/mechablast/) is separate by design.
 */

export const biz = {
  name: "JP Levi AI",
  parent: "JP LEVI INC.",
  parentNote: "a New Jersey corporation",
  person: "Robert Jean Pierre",
  role: "ML + AI Engineer",
  location: "North Brunswick, NJ",
  coords: "40.3907° N, 74.4721° W",
  email: "hello@jplevi.com",
  /** Three lines, with the first set larger, as in the reference. */
  headline: ["AI", "without the", "theater."],
  lead:
    "We design, build, and operate useful AI systems: RAG, GraphRAG, machine learning, automation, full-stack products, and managed infrastructure.",
  /** The four phases named under the hero CTA. */
  phases: ["Strategy", "Engineering", "Deployment", "Support"],
  teamLine: "A focused team of AI, software, product, and infrastructure specialists.",
  disciplines: ["AI Engineering", "Software Development", "Product Design", "Cloud Operations"],
  availability: "Taking on select projects",
  rail: "AI Engineering / Software / Infrastructure",
  url: "https://jplevi.com",
} as const;

export const bizRoutes = {
  home: "/",
  work: "/work/",
  capabilities: "/capabilities/",
  hosting: "/hosting/",
  about: "/about/",
  contact: "/contact/",
} as const;

export const bizNav = [
  { href: bizRoutes.work, label: "Work" },
  { href: bizRoutes.capabilities, label: "Services" },
  { href: bizRoutes.about, label: "Company" },
  { href: bizRoutes.contact, label: "Contact" },
] as const;

/** Hosting keeps its own page but reaches it through Services and the footer. */
export const bizFooterNav = [...bizNav.slice(0, 2), { href: bizRoutes.hosting, label: "Hosting" }, ...bizNav.slice(2)];

/** The three-stage pipeline annotated beside the hero. */
export const pipeline = [
  { id: "retrieval", title: "Retrieval", body: "Find the right context across your data." },
  { id: "reasoning", title: "Reasoning", body: "Infer, synthesize, and generate with precision." },
  { id: "deployment", title: "Deployment", body: "Ship, monitor, and operate with confidence." },
] as const;

/** The inverted band: what one engineer owning the whole stack actually covers. */
/** Inverted band heading. */
export const ownershipHeading = "Small team. End-to-end delivery.";

export const ownership = [
  { n: "01", label: "Private knowledge systems" },
  { n: "02", label: "Predictive ML" },
  { n: "03", label: "Business platforms" },
  { n: "04", label: "Managed deployment" },
] as const;

export type Service = {
  id: string;
  title: string;
  summary: string;
  /** Concrete deliverables, written as things you receive. */
  detail: string[];
  href: string;
};

export const services: Service[] = [
  {
    id: "rag",
    title: "Private knowledge systems",
    summary:
      "Retrieval-augmented generation over your own documents, so answers cite your material instead of guessing.",
    detail: [
      "Document ingestion, chunking, and embedding pipelines",
      "Vector and hybrid search tuned against your real queries",
      "GraphRAG when relationships between entities matter more than passages",
      "Answer evaluation, so you can prove quality before it ships",
    ],
    href: bizRoutes.capabilities,
  },
  {
    id: "genai",
    title: "Generative AI services",
    summary:
      "Assistants, extraction, summarisation, and classification built against your workflow rather than a demo.",
    detail: [
      "Model selection and prompt architecture with measured trade-offs",
      "Structured extraction from documents, email, and forms",
      "Agentic workflows with tool use, guardrails, and human review points",
      "Cost and latency budgets set before a line is written",
    ],
    href: bizRoutes.capabilities,
  },
  {
    id: "ml",
    title: "Machine learning solutions",
    summary:
      "Forecasting, scoring, and classification where a trained model beats a prompt on cost and accuracy.",
    detail: [
      "Feature engineering against your historical data",
      "Model training, validation, and honest error analysis",
      "Batch and real-time inference services",
      "Monitoring for drift once it is live",
    ],
    href: bizRoutes.capabilities,
  },
  {
    id: "web",
    title: "Web and product development",
    summary:
      "Full-stack applications with the AI parts built in, not bolted on afterwards.",
    detail: [
      "Next.js and TypeScript front ends",
      "APIs, auth, background jobs, and data modelling",
      "Admin tooling your team can actually operate",
      "Static or server-rendered, chosen for the job",
    ],
    href: bizRoutes.capabilities,
  },
  {
    id: "hosting",
    title: "Managed VPS hosting",
    summary:
      "Your applications running on infrastructure we provision, harden, monitor, and keep patched.",
    detail: [
      "Provisioning, hardening, and TLS",
      "Deploy pipelines and rollback",
      "Backups, uptime monitoring, and alerting",
      "Ongoing patching and capacity review",
    ],
    href: bizRoutes.hosting,
  },
  {
    id: "smb",
    title: "Small and mid-size business systems",
    summary:
      "Automation and internal tools for teams without an engineering department to lean on.",
    detail: [
      "Process automation across the tools you already pay for",
      "Internal dashboards and reporting",
      "Data cleanup and migration",
      "Documentation and handover, so nothing depends on us forever",
    ],
    href: bizRoutes.capabilities,
  },
];

/** How an engagement runs. Four steps, deliberately unglamorous. */
export const process = [
  {
    n: "01",
    title: "Scope",
    body: "A short paid discovery: what you have, what you need, and whether AI is even the right tool. You get a written recommendation either way.",
  },
  {
    n: "02",
    title: "Prototype",
    body: "The riskiest part built first, measured against real data of yours. If it does not clear the bar, you find out cheaply.",
  },
  {
    n: "03",
    title: "Build",
    body: "The production system, with tests, evaluation, and the operational pieces that keep it running after launch.",
  },
  {
    n: "04",
    title: "Operate",
    body: "Hosting, monitoring, and iteration, or a clean handover to your team with documentation. Your call.",
  },
];

/** Small technical readouts used as texture across the layouts. */
export const stack = [
  "Python", "TypeScript", "PyTorch", "Next.js", "Postgres", "pgvector",
  "Neo4j", "FastAPI", "Docker", "Linux", "Claude", "OpenAI",
];
