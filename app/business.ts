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


export type Capability = {
  id: string;
  n: string;
  /** Two lines on the rail card. */
  title: [string, string];
  /** Chip on the rail card. */
  tag: string;
  /** Status text in the workbench header. */
  status: string;
  /** One or two sentences in the workbench. */
  blurb: string;
  /** Left column of the workbench: what you receive. */
  delivers: string[];
  /** Right column: the technical shape of it. */
  meta: { label: string; value: string }[];
};

/**
 * The full range, not just retrieval. Order is the order on the rail.
 */
export const capabilities: Capability[] = [
  {
    id: "knowledge",
    n: "01",
    title: ["Private knowledge", "systems"],
    tag: "RAG · GraphRAG",
    status: "indexed",
    blurb:
      "Answers drawn from your own documents, with citations back to the source. Graph retrieval when the relationships between entities matter more than the passages.",
    delivers: [
      "Ingestion, parsing, and chunking pipelines",
      "Vector and hybrid search tuned on your queries",
      "GraphRAG over entities and relationships",
      "Evaluation sets that prove quality before launch",
    ],
    meta: [
      { label: "Retrieval", value: "vector · hybrid · graph" },
      { label: "Stores", value: "pgvector · Neo4j" },
      { label: "Evaluation", value: "golden sets · regression gates" },
    ],
  },
  {
    id: "mcp",
    n: "02",
    title: ["MCP servers", "and connectors"],
    tag: "Model Context Protocol",
    status: "connected",
    blurb:
      "Your systems exposed as tools any model can call. Built once against the protocol, then usable from Claude, an IDE, or your own application.",
    delivers: [
      "Tool, resource, and prompt definitions",
      "Auth, scoping, and audit trails",
      "Local stdio and remote HTTP transports",
      "Client wiring for the apps your team already uses",
    ],
    meta: [
      { label: "Transport", value: "stdio · http · sse" },
      { label: "Auth", value: "oauth2 · api key" },
      { label: "Clients", value: "Claude · IDEs · your apps" },
    ],
  },
  {
    id: "agents",
    n: "03",
    title: ["Agents and", "automation"],
    tag: "Tool use · workflows",
    status: "running",
    blurb:
      "Multi-step work that runs without a person driving it, with guardrails and human review at the points where being wrong is expensive.",
    delivers: [
      "Workflow design with explicit failure paths",
      "Tool use, retries, and budget ceilings",
      "Human-in-the-loop review gates",
      "Queues, schedulers, and background jobs",
    ],
    meta: [
      { label: "Runtime", value: "queues · cron · webhooks" },
      { label: "Controls", value: "budgets · timeouts · review" },
      { label: "Tracing", value: "per-step logs and replay" },
    ],
  },
  {
    id: "ml",
    n: "04",
    title: ["Machine", "learning"],
    tag: "Forecasting · scoring",
    status: "trained",
    blurb:
      "Trained models for the problems where a prompt is the wrong tool: forecasting, scoring, ranking, classification, anomaly detection.",
    delivers: [
      "Feature engineering on your historical data",
      "Training, validation, and honest error analysis",
      "Batch and real-time inference services",
      "Drift monitoring once it is live",
    ],
    meta: [
      { label: "Stack", value: "PyTorch · scikit-learn" },
      { label: "Serving", value: "batch · realtime API" },
      { label: "Monitoring", value: "drift · data quality" },
    ],
  },
  {
    id: "web",
    n: "05",
    title: ["Web and product", "engineering"],
    tag: "Full stack",
    status: "shipped",
    blurb:
      "Complete applications, front to back. The AI parts built in from the start rather than bolted on to something that cannot carry them.",
    delivers: [
      "Next.js and TypeScript front ends",
      "Auth, billing, roles, and admin tooling",
      "Design systems that survive a second developer",
      "Static or server-rendered, chosen per job",
    ],
    meta: [
      { label: "Front end", value: "Next.js · React · TS" },
      { label: "Back end", value: "Node · Python · FastAPI" },
      { label: "Delivery", value: "CI, preview builds, rollback" },
    ],
  },
  {
    id: "data",
    n: "06",
    title: ["APIs and data", "engineering"],
    tag: "Pipelines · integration",
    status: "synced",
    blurb:
      "The plumbing under everything else: moving data between systems that were never designed to talk, and keeping it correct.",
    delivers: [
      "REST and typed API design",
      "ETL and sync between third-party systems",
      "Schema design, migration, and cleanup",
      "Backfills that can be run twice safely",
    ],
    meta: [
      { label: "Databases", value: "Postgres · SQLite · Redis" },
      { label: "Integration", value: "webhooks · queues · cron" },
      { label: "Quality", value: "idempotent, replayable jobs" },
    ],
  },
  {
    id: "ops",
    n: "07",
    title: ["Managed hosting", "and operations"],
    tag: "VPS · DevOps",
    status: "99.9%",
    blurb:
      "Servers we provision, harden, monitor, and keep patched, so the thing that was built stays running after the build ends.",
    delivers: [
      "Provisioning, hardening, and TLS",
      "Deploy pipelines with a tested rollback",
      "Restore-tested backups, not just backups",
      "Uptime and error alerting that reaches a human",
    ],
    meta: [
      { label: "Platform", value: "Linux · Docker · nginx" },
      { label: "Delivery", value: "push to deploy · rollback" },
      { label: "Watch", value: "uptime · disk · errors" },
    ],
  },
  {
    id: "smb",
    n: "08",
    title: ["Internal tools", "for small teams"],
    tag: "SMB systems",
    status: "in use",
    blurb:
      "Automation and internal software for companies without an engineering department, built so the team can operate it without us.",
    delivers: [
      "Automation across tools you already pay for",
      "Dashboards and reporting people actually open",
      "Data cleanup and migration off spreadsheets",
      "Documentation and handover",
    ],
    meta: [
      { label: "Shape", value: "internal apps · automations" },
      { label: "Handover", value: "runbooks · training" },
      { label: "Lock-in", value: "none, the code is yours" },
    ],
  },
];

/**
 * How an engagement runs. Written to fit every capability on the rail: a hosting
 * migration and a Next.js build move through the same four phases as a
 * retrieval system.
 */
export type Phase = {
  n: string;
  title: string;
  duration: string;
  body: string;
  /** What actually lands in your hands when the phase ends. */
  output: [string, string];
};

export const process: Phase[] = [
  {
    n: "01",
    title: "Scope",
    duration: "1 week",
    body: "What you have, what you need, and whether the thing you asked for is the thing that solves it. Paid, short, and useful even if you stop here.",
    output: ["Written recommendation", "Fixed price and timeline"],
  },
  {
    n: "02",
    title: "Prove",
    duration: "1-2 weeks",
    body: "The riskiest part built first against your real data or systems, so the expensive unknown gets answered before the expensive work starts.",
    output: ["Working prototype", "A measured result, pass or fail"],
  },
  {
    n: "03",
    title: "Build",
    duration: "4-12 weeks",
    body: "The production system, with tests, deployment, and the unglamorous operational pieces that decide whether it survives its first month.",
    output: ["Deployed system", "Code in your repository"],
  },
  {
    n: "04",
    title: "Operate",
    duration: "Ongoing, or not",
    body: "Hosting, monitoring, and iteration for as long as it earns its keep, or a clean handover to your team. Chosen deliberately, not by default.",
    output: ["Runbook and training", "Support, or a clean exit"],
  },
];

/** Small technical readouts used as texture across the layouts. */
export const stack = [
  "Python", "TypeScript", "PyTorch", "Next.js", "Postgres", "pgvector",
  "Neo4j", "FastAPI", "Docker", "Linux", "Claude", "OpenAI",
];
