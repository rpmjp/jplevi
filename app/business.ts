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
  /** Deliberately no coordinates: they resolve to a precise spot. */
  /** Persistent CTA in the left rail. */
  railCta: "Consult with an expert",
  /** JP LEVI INC. incorporated in Newark, NJ on 2015-08-28. */
  founded: 2015,
  email: "hello@jplevi.com",
  phone: "(929) 356-4644",
  /** E.164 for the tel: link. */
  phoneHref: "+19293564644",
  /** Three lines, with the first set larger, as in the reference. */
  headline: ["AI", "that works for", "your business."],
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
  services: "/services/",
  hosting: "/hosting/",
  about: "/about/",
  contact: "/contact/",
} as const;

export const bizNav = [
  { href: bizRoutes.services, label: "Services" },
  { href: bizRoutes.hosting, label: "Hosting" },
  { href: bizRoutes.about, label: "Company" },
  { href: bizRoutes.contact, label: "Contact" },
] as const;

/**
 * Hosting keeps its own page but reaches it through Services and the footer.
 * Work is deliberately absent: the page still exists at bizRoutes.work, reached
 * from the gaming section rather than from the business nav.
 */
export const bizFooterNav = bizNav;

/** The three-stage pipeline annotated beside the hero. */
export const pipeline = [
  { id: "retrieval", title: "Retrieval", body: "Find the right context across your data." },
  { id: "reasoning", title: "Reasoning", body: "Infer, synthesize, and generate with precision." },
  { id: "deployment", title: "Deployment", body: "Ship, monitor, and operate with confidence." },
] as const;

/** The inverted band: what one engineer owning the whole stack actually covers. */
/** Inverted band heading. */
export const ownershipHeading = "Small team. End-to-end delivery";

export const ownership = [
  { n: "01", label: "Private knowledge systems" },
  { n: "02", label: "Predictive ML" },
  { n: "03", label: "Business platforms" },
  { n: "04", label: "Managed deployment" },
] as const;

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
  /** The same thing said without jargon, for a non-technical reader. */
  plain: string;
  /** Concrete situations, in the reader's own words. */
  scenarios: [string, string, string];
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
    plain:
      "A search box that answers questions about your own documents, and shows you which page it took the answer from.",
    scenarios: [
      "Staff keep asking each other the same questions and digging through folders to answer them",
      "You have years of contracts, reports or manuals and nobody can find anything in them",
      "You tried a public chatbot and it confidently invented things about your business",
    ],
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
    plain:
      "A standard plug that lets AI assistants use the systems you already have, safely, without handing your data over to them.",
    scenarios: [
      "You want staff to ask an assistant about live data sitting in your CRM or database",
      "You keep paying to rebuild the same integration every time a new tool appears",
      "You need a record of exactly what the assistant was allowed to see and do",
    ],
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
    plain:
      "Software that carries out multi-step jobs on its own, and stops to ask a person whenever being wrong would be expensive.",
    scenarios: [
      "Someone on your team spends hours moving information between systems",
      "A process is well understood but tedious, repetitive and easy to get wrong",
      "Work needs to happen overnight, on a schedule, or the moment something arrives",
    ],
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
    plain:
      "Predictions built from your own history: what is likely to sell, who is likely to leave, which record looks wrong.",
    scenarios: [
      "You forecast demand, stock or staffing with a spreadsheet and experience",
      "You want things ranked or scored automatically instead of by hand",
      "You need unusual or suspicious activity flagged before a person sees it",
    ],
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
    plain:
      "The application itself: what people see, what happens when they click, and everything running behind it.",
    scenarios: [
      "You need the thing built, not just advice about building it",
      "Your current site cannot do what the business has grown into needing",
      "You want the AI parts and the app around them built by the same people",
    ],
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
    plain:
      "Getting your systems to talk to each other, and keeping the numbers right while they do.",
    scenarios: [
      "Two systems hold the same information and quietly disagree",
      "Someone exports a spreadsheet by hand every week so a report can exist",
      "You are moving off spreadsheets, or off software you have outgrown",
    ],
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
    plain:
      "We run the servers your software lives on: keeping it online, backed up, patched and watched.",
    scenarios: [
      "Nobody in-house owns the servers, and it shows when something breaks",
      "You have backups but nobody has ever tried restoring one",
      "You want one number to call when the site goes down at 2am",
    ],
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
    plain:
      "Small pieces of software shaped around how your team already works, instead of bending the team around someone else's product.",
    scenarios: [
      "Your process lives in one person's head and a spreadsheet only they understand",
      "Off-the-shelf software does most of it and you work around the rest",
      "You need reporting your team will actually open and use",
    ],
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

/** The free entry point: written review first, a call only if it earns one. */
export const feasibility = {
  title: "Free feasibility review",
  lead:
    "Before anyone pays for anything, we will tell you in writing whether AI is the right tool for your problem, and what we would use instead if it is not.",
  steps: [
    {
      n: "01",
      title: "You send the problem",
      body: "A paragraph on what is not working, and roughly what data or systems you already have.",
    },
    {
      n: "02",
      title: "We send a written review",
      body: "An honest assessment: whether this is an AI problem, the shape of the work, and roughly what it would take. Yours to forward to whoever signs off.",
    },
    {
      n: "03",
      title: "A call, if it earns one",
      body: "If there is something worth building, we book thirty minutes. If not, you have a written answer and owe us nothing.",
    },
  ],
} as const;

/** What we commit to in writing. Deliberately modest so it is always met. */
export const guarantee = {
  headline: "We reply within one business day.",
  body: "Every serious enquiry gets a reply from a human within one business day, including the ones we turn down.",
  /** Short form for the inverted band. Three sentences, no filler. */
  question: "Why small businesses choose us",
  subhead: "AI, software, and the infrastructure underneath",
  pitch:
    "A reply within one business day. No account managers, no handoffs, no taking one slice of the problem. You talk to the engineer who writes the code and sets up the server it lives on.",
} as const;

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

/**
 * The self-serve half of the free feasibility review, rendered in the panel on
 * the home page.
 *
 * Written as the complaint an owner would actually say out loud, not as the
 * diagnosis we would write afterwards. Nobody arrives wanting RAG or MCP, so
 * neither appears on a chip: those are how the work gets built, and they belong
 * in the readout at most. The plain web, hosting and integration jobs lead the
 * list on purpose, because they are the highest-volume asks and the usual first
 * job before anyone buys anything with a model in it.
 *
 * `verdict` is only ever about which tool is right. Every answer is a yes.
 */
export type FeasibilityProbe = {
  id: string;
  /** Chip label, first person. Kept under ~36 characters so two sit per row. */
  label: string;
  verdict: "ai" | "not-ai";
  /** The headline answer. Always opens with the yes. */
  call: string;
  /** One or two sentences in the same plain register as the chip. */
  read: string;
  /** Which capability on /services/ the readout links to. */
  capabilityId: string;
};

export const feasibilityProbes: FeasibilityProbe[] = [
  {
    id: "website",
    label: "I need a new website",
    verdict: "not-ai",
    call: "Full rebuild, front to back.",
    read: "A site built front to back that your own team can update afterwards. If AI belongs in it later, it goes in properly rather than bolted onto something that cannot carry it.",
    capabilityId: "web",
  },
  {
    id: "hosting",
    label: "My hosting is slow and overpriced",
    verdict: "not-ai",
    call: "Migrate it, keep the rankings.",
    read: "Server, certificate, and a deploy that can be rolled back, then the move across with the redirects already in place so nothing that ranks today disappears tomorrow.",
    capabilityId: "ops",
  },
  {
    id: "fivesystems",
    label: "The same details live in five systems",
    verdict: "not-ai",
    call: "One sync, running on schedule.",
    read: "Your systems taught to hand information to each other on a schedule, safe to run twice, so one update lands everywhere instead of getting retyped four more times.",
    capabilityId: "data",
  },
  {
    id: "faq",
    label: "Customers ask the same five questions",
    verdict: "ai",
    call: "Retrieval over your own material.",
    read: "Hours, availability, order status and policy answered out of your own material with the source attached, and handed straight to a person the moment a question is not one of the easy five.",
    capabilityId: "knowledge",
  },
  {
    id: "chatbot",
    label: "I need a custom chatbot",
    verdict: "ai",
    call: "Grounded in your own documents.",
    read: "The hard part is never the chat box, it is what the thing is allowed to say. Built over your own material, citing its source, and handing over to a person the moment it is out of its depth.",
    capabilityId: "knowledge",
  },
  {
    id: "integration",
    label: "AI integration in my company",
    verdict: "ai",
    call: "A connector layer, built once.",
    read: "Your systems exposed once as tools an assistant can call, with scoping, auth and an audit trail, so it works from Claude or from your own app and your data never leaves where it lives.",
    capabilityId: "mcp",
  },
  {
    id: "automate",
    label: "I want to automate my business",
    verdict: "ai",
    call: "Start with one repeatable job.",
    read: "Automation across the tools you already pay for, built so your own team can run it unaided, with the runbook that keeps it working long after the handover.",
    capabilityId: "smb",
  },
  {
    id: "agentic",
    label: "Agents, not another chat box",
    verdict: "ai",
    call: "Multi-step runs, with guardrails.",
    read: "Multi-step work that runs without a person driving it, with budgets, retries and a record of what it did, stopping for review at the points where being wrong is expensive.",
    capabilityId: "agents",
  },
  {
    id: "predictions",
    label: "Machine learning and forecasting",
    verdict: "ai",
    call: "Trained on your own history.",
    read: "Forecasting, scoring and anomaly flags trained on your own numbers rather than on a generic model. If that history was never kept, you will hear that before spending anything.",
    capabilityId: "ml",
  },
];

/** Copy for the panel around the probes. */
export const feasibilityPanel = {
  title: "Free feasibility review",
  status: "Reply in 1 business day",
  step1: "What do you need help with?",
  step2: "Anything you want to add",
  placeholder: "A sentence or two. What you have tried, what data you hold.",
  prompt: "Pick whichever sounds most like you. You get a straight answer, including when the answer is that you do not need AI for it.",
  cta: "Send it to " + biz.email,
  ctaIdle: "Pick one to continue",
  foot: "You get a written answer, yours to forward. No call required, no cost, and we say so when the answer is no.",
} as const;

/**
 * The people on the business side. A list, so partners and staff are a data
 * change rather than a redesign. `photo` is a cutout with a transparent
 * background; someone without one still renders, just without the portrait.
 */
export type Person = {
  id: string;
  name: string;
  role: string;
  location: string;
  /** Cutout portrait, transparent background. Null until there is one. */
  photo: string | null;
  photoFallback?: string;
  /** First person, because a company page written in the third person about
   *  one person reads as a company hiding behind a pronoun. */
  line: string;
};

export const people: Person[] = [
  {
    id: "robert",
    name: "Robert Jean Pierre",
    role: "ML + AI Engineer",
    location: "North Brunswick, NJ",
    photo: "/robert-jean-pierre.webp",
    photoFallback: "/robert-jean-pierre.png",
    line: "I write the code, train the models, and set up the server it runs on. When you call the number on this site, I am who answers.",
  },
];

/** Dated facts. Nothing here is rounded up, inferred, or decorative. */
export const milestones = [
  { year: "1996", label: "First line of code" },
  { year: "2015", label: "JP LEVI INC. incorporated" },
  { year: "2020", label: "Into machine learning" },
  { year: "2026", label: "MS Computer Science" },
] as const;

export const credentials = [
  { label: "Masters", value: "Computer Science", where: "NJIT", note: "AI and machine learning" },
  { label: "Bachelors", value: "Computer Science", where: "Rutgers", note: "AI and machine learning" },
  { label: "Previously", value: "Verizon", where: "", note: "" },
] as const;

/** How this site is built. True of the page you are reading it on. */
export const colophon = [
  { k: "Framework", v: "Next.js, static export" },
  { k: "Type", v: "Archivo Narrow, IBM Plex Mono" },
  { k: "Delivery", v: "Build gate on every push" },
  { k: "Runtime", v: "No server, no tracking" },
] as const;

/**
 * Work worth showing. Empty on purpose: the About page renders this section
 * only when there is something in it, so adding an entry is a data change.
 */
export type ShippedItem = { id: string; name: string; body: string };

export const shipped: ShippedItem[] = [];
