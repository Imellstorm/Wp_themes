/* global window, React, ReactDOM */
// devpoint — single post page

const { useState, useEffect, useMemo, useRef, useCallback } = React;

const ARTICLES = window.ARTICLES;
const CATEGORIES = window.CATEGORIES;
const SAMPLE_BODY = window.SAMPLE_BODY;
const ThumbArt = window.ThumbArt;
const I = window.Icons;
const initials = window.AUTHOR_INITIALS;

const params = new URLSearchParams(location.search);
const startId = params.get("id");
const startArticle = ARTICLES.find(a => a.id === startId) || ARTICLES[0];

function Avatar({ author, size = 22 }) {
  return (
    <span className="avatar" style={{
      background: author.color, width: size, height: size, fontSize: size * 0.45
    }}>{initials(author.name)}</span>
  );
}

function CardMeta({ a }) {
  return (
    <div className="card-meta">
      <a className="author-tag" href={`author.html?id=${a.author.id}`}
         onClick={(e) => e.stopPropagation()}>
        <Avatar author={a.author} />
        <span className="author-name">{a.author.name}</span>
      </a>
      <span className="meta-time">{a.read} min read</span>
    </div>
  );
}

function ArticleCard({ a, onOpen }) {
  return (
    <button className="card" onClick={() => onOpen(a)}>
      <div className="card-thumb">
        <ThumbArt {...a.thumb} />
        <a className="card-cat" href={`category.html?id=${a.cat.id}`}
           onClick={(e) => e.stopPropagation()}>{a.cat.name}</a>
        <span className="card-date">{a.date}</span>
      </div>
      <h3 title={a.title}>{a.title}</h3>
      <p title={a.excerpt}>{a.excerpt}</p>
      <CardMeta a={a} />
    </button>
  );
}

function Header({ onSearchOpen, scrolled }) {
  return (
    <div className={`hdr-shell${scrolled ? " scrolled" : ""}`}>
      <div className="wrap hdr hdr-simple">
        <a href="index.html" className="brand" aria-label="devpoint">
          <span className="brand-mark">d</span>
          <span>devpoint</span>
        </a>
        <button className="search-box-desk hdr-search" onClick={onSearchOpen} aria-label="Search">
          <I.search size={16} />
          <input placeholder="Search articles…" readOnly />
          <kbd>⌘K</kbd>
        </button>
        <button className="icon-btn hdr-search-mobile" onClick={onSearchOpen} aria-label="Search">
          <I.search size={18} />
        </button>
      </div>
    </div>
  );
}

function ContactCTA() {
  return (
    <section className="section section-newsletter">
      <div className="wrap">
        <div className="newsletter">
          <div className="newsletter-inner">
            <div>
              <div className="nl-eyebrow">Work with us</div>
              <h2>If you would need any development feel <em>free to contact me</em>.</h2>
              <p>I'm Bram, founder of OGD Solutions — the team behind devpoint. We design and build websites, mobile apps and internal tools. Drop a line on WhatsApp or email and let's talk.</p>
            </div>
            <div className="contact-actions">
              <a href="https://wa.me/15551234567" target="_blank" rel="noopener" className="btn btn-cta btn-lg">
                <I.wa size={18} /> Message on WhatsApp
              </a>
              <a href="mailto:hello@ogd.solutions" className="contact-secondary">
                <I.arrow size={14} /> hello@ogd.solutions
              </a>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

function Footer() {
  const [email, setEmail] = useState("");
  const [sent, setSent] = useState(false);
  const [err, setErr] = useState("");
  const submit = (e) => {
    e.preventDefault(); setErr("");
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
      setErr("Please enter a valid email address."); return;
    }
    try { localStorage.setItem("devpoint-newsletter", email); } catch (_) {}
    setSent(true);
  };
  return (
    <footer className="ftr">
      <div className="wrap">
        <div className="ftr-grid">
          <div>
            <a href="index.html" className="brand">
              <span className="brand-mark">d</span>
              <span>devpoint<small>by OGD Solutions</small></span>
            </a>
            <p className="ftr-brand-blurb">
              A reading-room about shipping websites, apps and the businesses around them. Published by the team at OGD Solutions.
            </p>
          </div>
          <div className="ftr-subscribe">
            <h4>Subscribe to the newsletter</h4>
            {sent ? (
              <div className="ftr-sub-success">
                <I.check size={16} /> Thanks — check {email} for confirmation.
              </div>
            ) : (
              <form className="ftr-sub-form" onSubmit={submit}>
                <input type="email" inputMode="email" placeholder="you@yourcompany.com"
                       value={email} onChange={(e) => { setEmail(e.target.value); setErr(""); }} />
                <button type="submit">Subscribe</button>
              </form>
            )}
            {err && <div className="ftr-sub-err">{err}</div>}
          </div>
          <div className="ftr-col">
            <h4>Read</h4>
            <ul>
              <li><a href="index.html#all">Latest essays</a></li>
              <li><a href="#">Most-read</a></li>
              <li><a href="#">Archive</a></li>
              <li><a href="#">RSS feed</a></li>
            </ul>
          </div>
        </div>
        <div className="ftr-base">
          <span>© 2026 devpoint, a publication by OGD Solutions.</span>
          <nav>
            <a href="#">Privacy</a>
            <a href="#">Terms</a>
            <a href="#">RSS</a>
          </nav>
        </div>
      </div>
    </footer>
  );
}

function NewsletterInline_UNUSED() {
  const [email, setEmail] = useState("");
  const [sent, setSent] = useState(false);
  const [err, setErr] = useState("");
  const submit = (e) => {
    e.preventDefault(); setErr("");
    if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
      setErr("Please enter a valid email address."); return;
    }
    try { localStorage.setItem("ogd-newsletter", email); } catch (_) {}
    setSent(true);
  };
  return (
    <section className="section section-newsletter">
      <div className="wrap">
        <div className="newsletter">
          <div className="newsletter-inner">
            <div>
              <div className="nl-eyebrow">Newsletter · Every other Thursday</div>
              <h2>One essay. Straight to <em>your inbox</em>.</h2>
              <p>The field-tested playbooks we share with our clients — pricing math, scoping templates, and the trade-offs nobody puts in their pitch deck. No spam, unsubscribe in one click.</p>
            </div>
            {sent ? (
              <div className="nl-success">
                <I.check size={20} />
                <div><b>You're in.</b> Check {email} for a confirmation email.</div>
              </div>
            ) : (
              <form className="nl-form" onSubmit={submit}>
                <div className="nl-input-row">
                  <input type="email" inputMode="email" placeholder="you@yourcompany.com"
                         value={email} onChange={(e) => { setEmail(e.target.value); setErr(""); }} />
                  <button type="submit">Subscribe</button>
                </div>
                {err
                  ? <div className="nl-fine" style={{ color: "#F4C8B5" }}>{err}</div>
                  : <div className="nl-fine">2,400+ readers already in.</div>
                }
              </form>
            )}
          </div>
        </div>
      </div>
    </section>
  );
}

function SearchOverlay({ open, onClose }) {
  const [q, setQ] = useState("");
  const [focused, setFocused] = useState(0);
  const inputRef = useRef(null);
  useEffect(() => {
    if (open) { setTimeout(() => inputRef.current?.focus(), 30); setQ(""); setFocused(0); }
  }, [open]);
  const results = useMemo(() => {
    if (!q.trim()) return ARTICLES.slice(0, 6);
    const k = q.toLowerCase();
    return ARTICLES.filter(a =>
      a.title.toLowerCase().includes(k) ||
      a.excerpt.toLowerCase().includes(k) ||
      a.cat.name.toLowerCase().includes(k)
    ).slice(0, 8);
  }, [q]);
  const goTo = (a) => { location.href = `post.html?id=${a.id}`; };
  const onKey = (e) => {
    if (e.key === "Escape") onClose();
    else if (e.key === "ArrowDown") { e.preventDefault(); setFocused(f => Math.min(f + 1, results.length - 1)); }
    else if (e.key === "ArrowUp")   { e.preventDefault(); setFocused(f => Math.max(f - 1, 0)); }
    else if (e.key === "Enter")     { e.preventDefault(); if (results[focused]) goTo(results[focused]); }
  };
  if (!open) return null;
  const hl = (text) => {
    if (!q.trim()) return text;
    const i = text.toLowerCase().indexOf(q.toLowerCase());
    if (i < 0) return text;
    return <>{text.slice(0, i)}<mark>{text.slice(i, i + q.length)}</mark>{text.slice(i + q.length)}</>;
  };
  return (
    <div className="search-overlay" onClick={onClose}>
      <div className="search-panel" onClick={(e) => e.stopPropagation()} onKeyDown={onKey}>
        <div className="search-input-row">
          <I.search size={18} />
          <input ref={inputRef} placeholder="Search essays, categories, authors…"
                 value={q} onChange={(e) => { setQ(e.target.value); setFocused(0); }} />
          <span className="esc">esc</span>
        </div>
        <div className="search-results">
          <div className="search-section-label">
            {q.trim() ? `${results.length} result${results.length === 1 ? "" : "s"}` : "Suggested for you"}
          </div>
          {results.map((a, i) => (
            <button key={a.id}
                    className={`search-result${i === focused ? " focused" : ""}`}
                    onMouseEnter={() => setFocused(i)}
                    onClick={() => goTo(a)}>
              <div className="sr-thumb"><ThumbArt {...a.thumb} /></div>
              <div className="sr-body">
                <div className="sr-title">{hl(a.title)}</div>
                <div className="sr-meta">
                  <span>{a.cat.name}</span><span className="divider-dot" /><span>{a.read} min</span>
                </div>
              </div>
              <I.arrow size={14} style={{ color: "var(--ink-3)" }} />
            </button>
          ))}
        </div>
      </div>
    </div>
  );
}

function Post() {
  const [scrolled, setScrolled] = useState(false);
  const [progress, setProgress] = useState(0);
  const [searchOpen, setSearchOpen] = useState(false);
  const a = startArticle;

  useEffect(() => {
    const onScroll = () => {
      setScrolled(window.scrollY > 8);
      const max = document.documentElement.scrollHeight - window.innerHeight;
      setProgress(max > 0 ? (window.scrollY / max) * 100 : 0);
    };
    window.addEventListener("scroll", onScroll);
    onScroll();
    return () => window.removeEventListener("scroll", onScroll);
  }, []);

  useEffect(() => {
    const onKey = (e) => {
      if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === "k") {
        e.preventDefault(); setSearchOpen(true);
      }
    };
    window.addEventListener("keydown", onKey);
    return () => window.removeEventListener("keydown", onKey);
  }, []);

  useEffect(() => {
    document.title = `${a.title} — devpoint`;
  }, [a]);

  const related = ARTICLES.filter(x => x.id !== a.id && x.cat.id === a.cat.id).slice(0, 3);
  const open = (r) => { location.href = `post.html?id=${r.id}`; };

  return (
    <>
      <Header scrolled={scrolled} onSearchOpen={() => setSearchOpen(true)} />
      <div className="post-progress" style={{ width: `${progress}%` }} />

      <main>
        <article className="post-page">
          <div className="wrap post-head-wrap">
            <nav className="breadcrumbs" aria-label="Breadcrumb">
              <ol>
                <li><a href="index.html">Home</a><span className="crumb-sep" aria-hidden="true">/</span></li>
                <li><a href={`category.html?id=${a.cat.id}`}>{a.cat.name}</a><span className="crumb-sep" aria-hidden="true">/</span></li>
                <li><span aria-current="page" title={a.title}>{a.title}</span></li>
              </ol>
            </nav>
            <h1 className="reader-title post-title">{a.title}</h1>
            <p className="reader-lead post-lead">{a.excerpt}</p>
            <div className="reader-byline post-byline">
              <a href={`author.html?id=${a.author.id}`} style={{ display: "flex", alignItems: "center", gap: 14, color: "inherit" }}>
                <Avatar author={a.author} size={44} />
                <div>
                  <div><b>{a.author.name}</b> · <span style={{ color: "var(--ink-3)" }}>{a.author.role}</span></div>
                  <div style={{ color: "var(--ink-3)", fontSize: 13 }}>
                    {a.date} <span className="reader-meta-sep">·</span> {a.read} min read
                  </div>
                </div>
              </a>
              <div style={{ marginLeft: "auto", display: "flex", gap: 8 }}>
                <button className="icon-btn" aria-label="Bookmark"><I.bookmark size={16} /></button>
              </div>
            </div>
          </div>

          <div className="wrap post-hero-wrap">
            <div className="reader-hero post-hero">
              <ThumbArt {...a.thumb} />
            </div>
          </div>

          <div className="wrap post-body-wrap">
            <div className="reader-content post-content">
              {SAMPLE_BODY.map((b, i) => {
                if (b.type === "p")     return <p key={i}>{b.text}</p>;
                if (b.type === "h2")    return <h2 key={i}>{b.text}</h2>;
                if (b.type === "quote") return <blockquote key={i}>{b.text}</blockquote>;
                if (b.type === "ul")    return <ul key={i}>{b.items.map((it, j) => <li key={j}>{it}</li>)}</ul>;
                return null;
              })}
            </div>

            <div className="reader-cta post-cta">
              <h3>Enjoyed this essay?</h3>
              <p>devpoint is published by the team at OGD Solutions, where we design and build sites and apps for a living.</p>
            </div>
          </div>
        </article>

        {related.length > 0 && (
          <section className="section">
            <div className="wrap">
              <div className="section-h">
                <div>
                  <div className="eyebrow">Keep reading</div>
                  <h2>More in <em>{a.cat.name}</em></h2>
                </div>
              </div>
              <div className="latest-grid post-related-grid">
                {related.map(r => <ArticleCard key={r.id} a={r} onOpen={open} />)}
              </div>
            </div>
          </section>
        )}

      </main>

      <ContactCTA />

      <Footer />

      <SearchOverlay open={searchOpen} onClose={() => setSearchOpen(false)} />
    </>
  );
}

ReactDOM.createRoot(document.getElementById("root")).render(<Post />);
