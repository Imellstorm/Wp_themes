/* global window */
// Data for OGD Solutions blog — articles, categories, hero data.

window.CATEGORIES = [
  { id: "web",     name: "Web Development",   short: "Web",      color: "#D67A52", count: 42, glyph: "globe" },
  { id: "mobile",  name: "Mobile Apps",        short: "Mobile",   color: "#5D8A6A", count: 28, glyph: "phone" },
  { id: "biz",     name: "IT for Business",    short: "Business", color: "#8B5E83", count: 35, glyph: "briefcase" },
  { id: "pm",      name: "Project Management", short: "Process",  color: "#E0A248", count: 19, glyph: "clipboard" },
  { id: "cases",   name: "Case Studies",       short: "Cases",    color: "#3F6E81", count: 14, glyph: "trophy" },
];

const C = Object.fromEntries(window.CATEGORIES.map(c => [c.id, c]));

// Author = id, initials, accent color
const A = {
  ms: { id: "ms", name: "Mira Sokolova",   role: "Editor-in-chief",  color: "#D67A52", bio: "Editor-in-chief at devpoint. 12 years writing about how websites earn their keep — last 6 leading editorial at OGD Solutions." },
  jp: { id: "jp", name: "Jonas Pries",     role: "Mobile Lead",      color: "#5D8A6A", bio: "Mobile lead at OGD Solutions. Shipped 30+ apps across iOS and Android. Writes about the messy reality between idea and App Store." },
  ad: { id: "ad", name: "Amelia Dorne",    role: "Strategy",         color: "#8B5E83", bio: "Strategy partner at OGD Solutions. Helps founders separate \"what users say\" from \"what users do.\" Ex-McKinsey, recovering." },
  tk: { id: "tk", name: "Tarek Khalid",    role: "PM",               color: "#3F6E81", bio: "Senior PM at OGD Solutions. Runs the projects nobody else wants to run, and writes about why they go off the rails." },
  rn: { id: "rn", name: "Reyna Nair",      role: "Sr. Engineer",     color: "#E0A248", bio: "Senior engineer at OGD Solutions. Builds the parts you don't see — and writes about why that's where 80% of the cost lives." },
  bk: { id: "bk", name: "Bram Kessler",    role: "Founder",          color: "#1E1916", bio: "Founder of OGD Solutions, publisher of devpoint. Started his first agency in 2016. Still ships code on Fridays." },
};
window.AUTHORS = A;

const initials = (name) => name.split(" ").map(s => s[0]).slice(0, 2).join("").toUpperCase();
window.AUTHOR_INITIALS = initials;

window.ARTICLES = [
  // ── Featured (4) ────────────────────────────────────────────────────────
  {
    id: "site-cost-2026",
    featured: true,
    cat: C.web,
    title: "How much does a website cost in 2026 — a no-jargon guide",
    excerpt: "A practical breakdown of what you're really paying for: scope, complexity, integrations, and the hidden costs that surprise most founders.",
    author: A.ms, date: "May 4, 2026", read: 9,
    thumb: { style: "stack", tones: ["#D67A52", "#F4D8C8", "#FBF7EF"] },
    body: "intro",
  },
  {
    id: "app-launch",
    featured: true,
    cat: C.mobile,
    title: "From idea to App Store — the launch playbook we use with 30+ founders",
    excerpt: "The four phases that decide whether your app gets stuck in development hell or shipped to real users. With timelines.",
    author: A.jp, date: "Apr 29, 2026", read: 12,
    thumb: { style: "phone", tones: ["#5D8A6A", "#D5E2D2", "#FBF7EF"] },
    body: "intro",
  },
  {
    id: "what-is-mvp",
    featured: true,
    cat: C.biz,
    title: "What an MVP actually is — and the 3 traps founders fall into",
    excerpt: "An MVP isn't \"a smaller version of your product.\" It's a learning instrument. Here's how to scope one that earns its name.",
    author: A.ad, date: "Apr 22, 2026", read: 7,
    thumb: { style: "rings", tones: ["#8B5E83", "#E7D2E2", "#FBF7EF"] },
    body: "intro",
  },
  {
    id: "choose-partner",
    featured: true,
    cat: C.biz,
    title: "How to choose your development partner — 7 red flags & 4 green ones",
    excerpt: "Cheap quotes, vague timelines, no portfolio depth: a quick framework for vetting agencies before you sign anything.",
    author: A.bk, date: "Apr 18, 2026", read: 8,
    thumb: { style: "grid", tones: ["#3F6E81", "#C8DAE0", "#FBF7EF"] },
    body: "intro",
  },

  // ── Latest (9+) ─────────────────────────────────────────────────────────
  {
    id: "trust-details",
    cat: C.web,
    title: "Designing for trust — the small details that close sales",
    excerpt: "Microcopy, social proof placement, and the friction patterns that quietly convert browsers into buyers.",
    author: A.ms, date: "May 8, 2026", read: 6,
    thumb: { style: "circles", tones: ["#D67A52", "#F4D8C8", "#FBF7EF"] },
  },
  {
    id: "headless",
    cat: C.web,
    title: "Headless CMS, explained without the jargon",
    excerpt: "When it actually helps your business, when it just adds cost, and how to decide for your team.",
    author: A.rn, date: "May 6, 2026", read: 5,
    thumb: { style: "layers", tones: ["#E0A248", "#F8E5C2", "#FBF7EF"] },
  },
  {
    id: "landing-conversion",
    cat: C.biz,
    title: "Why your landing page isn't converting — diagnosed in 8 steps",
    excerpt: "A teardown checklist we run on every client homepage. Most fail on point #3.",
    author: A.ad, date: "May 3, 2026", read: 7,
    thumb: { style: "bars", tones: ["#8B5E83", "#E7D2E2", "#FBF7EF"] },
  },
  {
    id: "ai-support",
    cat: C.biz,
    title: "AI in customer support — where it actually works (and where it doesn't)",
    excerpt: "An honest field report from 12 deployments. The wins are smaller and stranger than the demos suggest.",
    author: A.bk, date: "May 1, 2026", read: 9,
    thumb: { style: "scatter", tones: ["#5D8A6A", "#D5E2D2", "#FBF7EF"] },
  },
  {
    id: "cheap-dev-cost",
    cat: C.pm,
    title: "The hidden cost of cheap development — a $40k retrospective",
    excerpt: "We rebuilt a $12k app for $40k. Here's where every dollar of the difference went and why.",
    author: A.tk, date: "Apr 27, 2026", read: 11,
    thumb: { style: "stack", tones: ["#3F6E81", "#C8DAE0", "#FBF7EF"] },
  },
  {
    id: "rebuild-refactor",
    cat: C.pm,
    title: "Rebuild or refactor? A decision tree for product owners",
    excerpt: "Three questions to ask before greenlighting a v2. The answers usually surprise the team.",
    author: A.rn, date: "Apr 24, 2026", read: 6,
    thumb: { style: "tree", tones: ["#E0A248", "#F8E5C2", "#FBF7EF"] },
  },
  {
    id: "apple-2026",
    cat: C.mobile,
    title: "Apple's 2026 App Store changes — what founders need to know",
    excerpt: "Pricing, review timelines, and the new \"Essentials\" tier. We translated the developer release notes into plain English.",
    author: A.jp, date: "Apr 20, 2026", read: 8,
    thumb: { style: "phone", tones: ["#1E1916", "#F4D8C8", "#FBF7EF"] },
  },
  {
    id: "stripe-paypal",
    cat: C.biz,
    title: "Stripe vs PayPal in 2026 — which one fits your business",
    excerpt: "Fees, dispute handling, integration time. A clear comparison rather than a feature-list dump.",
    author: A.ad, date: "Apr 16, 2026", read: 6,
    thumb: { style: "split", tones: ["#5D8A6A", "#D67A52", "#FBF7EF"] },
  },
  {
    id: "fintech-case",
    cat: C.cases,
    title: "Case study — how we 3× conversions for a Berlin fintech in 6 weeks",
    excerpt: "Audit, hypothesis, three sprints, and one counter-intuitive copy change that did most of the work.",
    author: A.bk, date: "Apr 12, 2026", read: 10,
    thumb: { style: "chart", tones: ["#8B5E83", "#E7D2E2", "#FBF7EF"] },
  },
  {
    id: "saas-onboarding",
    cat: C.cases,
    title: "Onboarding redesign — a SaaS dashboard's activation rate +41%",
    excerpt: "Before/after metrics, the seven decisions that moved the needle, and the ones that didn't.",
    author: A.ms, date: "Apr 8, 2026", read: 9,
    thumb: { style: "grid", tones: ["#D67A52", "#F4D8C8", "#FBF7EF"] },
  },
];

// Long-form body content (shared for all articles — placeholder structure)
window.SAMPLE_BODY = [
  { type: "p", text: "Most founders we talk to have the same question — but they ask it in different shapes. \"How much does this cost?\" \"How long until launch?\" \"Will it actually move the metric we care about?\" Underneath, the real question is always the same: how do I make a confident decision when I'm not technical?" },
  { type: "p", text: "This piece is the answer we give every time. It's the conversation we'd have over coffee, written down so you can read it on your own pace and come back to it when you need to." },
  { type: "h2", text: "Start with the outcome, not the feature list" },
  { type: "p", text: "The most expensive mistake we see is scoping a project by features. You end up with a Notion doc full of checkboxes, a quote from each agency that's wildly different, and no way to compare them — because nobody is solving the same problem." },
  { type: "p", text: "Instead, lead with the outcome. What does success look like 90 days after launch? Pin that down, and the right scope falls out naturally." },
  { type: "quote", text: "Every line item in a proposal should earn its place by serving the outcome — not the other way around." },
  { type: "h2", text: "Where the budget actually goes" },
  { type: "p", text: "When we break down a typical project, the surprise isn't where the money is — it's where it isn't. Design and engineering combined are usually 55–65% of the budget. The rest goes to discovery, infrastructure, content, QA, and the small but reliable surprises that show up in week four of every project." },
  { type: "ul", items: [
    "Discovery & scoping — 8 to 12% of total",
    "Design — 18 to 25%",
    "Engineering — 35 to 45%",
    "Infrastructure, devops, integrations — 8 to 12%",
    "QA, accessibility, launch — 10 to 14%",
  ]},
  { type: "h2", text: "What to ask before signing" },
  { type: "p", text: "Three questions separate a partner from a vendor. Ask them in your first conversation and watch how the answers change the dynamic." },
  { type: "p", text: "First: \"What would you push back on in our brief?\" Second: \"Who on your team will I actually work with?\" Third: \"What does month two look like if we hit a wall?\" If the answers are vague, the project will be too." },
];

window.HERO = {
  badge: "Issue 47",
  badgeText: "Spring playbook — 12 fresh essays on shipping in 2026",
  lead: "Field-tested essays on websites, apps, and how to actually launch them. Plain language, no jargon, written by people who ship for a living.",
  stats: [
    { n: "140+", l: "Essays in the archive" },
    { n: "2.4k", l: "Readers every Thursday" },
    { n: "8 min", l: "Average read length" },
  ],
};

window.MAIN_SITE_URL = "https://ogd.solutions";
window.MAIN_SITE_LABEL = "ogd.solutions";
