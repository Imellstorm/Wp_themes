/* global window, React, ReactDOM */
// devpoint — shared chrome (header, footer, contact CTA, search overlay)
// used by post / category / author pages. Returns components on window.

const { useState, useEffect, useMemo, useRef } = React;

const _ARTICLES = window.ARTICLES;
const _CATEGORIES = window.CATEGORIES;
const _I = window.Icons;
const _ThumbArt = window.ThumbArt;
const _initials = window.AUTHOR_INITIALS;

function Avatar({ author, size = 22 }) {
  return (
    <span className="avatar" style={{
      background: author.color, width: size, height: size, fontSize: size * 0.45
    }}>{_initials(author.name)}</span>
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

function ArticleCard({ a }) {
  const go = () => { location.href = `post.html?id=${a.id}`; };
  return (
    <button className="card" onClick={go}>
      <div className="card-thumb">
        <_ThumbArt {..._toThumb(a.thumb)} />
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
function _toThumb(t) { return t; }

function Breadcrumbs({ items }) {
  return (
    <nav className="breadcrumbs" aria-label="Breadcrumb">
      <ol>
        {items.map((it, i) => (
          <li key={i}>
            {it.href
              ? <a href={it.href}>{it.label}</a>
              : <span aria-current="page" title={it.label}>{it.label}</span>}
            {i < items.length - 1 && <span className="crumb-sep" aria-hidden="true">/</span>}
          </li>
        ))}
      </ol>
    </nav>
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
          <_I.search size={16} />
          <input placeholder="Search articles…" readOnly />
          <kbd>⌘K</kbd>
        </button>
        <button className="icon-btn hdr-search-mobile" onClick={onSearchOpen} aria-label="Search">
          <_I.search size={18} />
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
                <_I.wa size={18} /> Message on WhatsApp
              </a>
              <a href="mailto:hello@ogd.solutions" className="contact-secondary">
                <_I.arrow size={14} /> hello@ogd.solutions
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
                <_I.check size={16} /> Thanks — check {email} for confirmation.
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

function SearchOverlay({ open, onClose }) {
  const [q, setQ] = useState("");
  const [focused, setFocused] = useState(0);
  const inputRef = useRef(null);
  useEffect(() => {
    if (open) { setTimeout(() => inputRef.current?.focus(), 30); setQ(""); setFocused(0); }
  }, [open]);
  const results = useMemo(() => {
    if (!q.trim()) return _ARTICLES.slice(0, 6);
    const k = q.toLowerCase();
    return _ARTICLES.filter(a =>
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
          <_I.search size={18} />
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
              <div className="sr-thumb"><_ThumbArt {...a.thumb} /></div>
              <div className="sr-body">
                <div className="sr-title">{hl(a.title)}</div>
                <div className="sr-meta">
                  <span>{a.cat.name}</span><span className="divider-dot" /><span>{a.read} min</span>
                </div>
              </div>
              <_I.arrow size={14} style={{ color: "var(--ink-3)" }} />
            </button>
          ))}
        </div>
      </div>
    </div>
  );
}

// Standard listing page wrapper used by category + author pages:
// header → page-head block → grid of cards → contact CTA → footer.
function ListingPage({ breadcrumbs, head, articles, empty }) {
  const [scrolled, setScrolled] = useState(false);
  const [searchOpen, setSearchOpen] = useState(false);
  useEffect(() => {
    const onScroll = () => setScrolled(window.scrollY > 8);
    window.addEventListener("scroll", onScroll);
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
  return (
    <>
      <Header scrolled={scrolled} onSearchOpen={() => setSearchOpen(true)} />
      <main>
        <section className="page-head">
          <div className="wrap">
            <Breadcrumbs items={breadcrumbs} />
            {head}
          </div>
        </section>
        <section className="section section-grid">
          <div className="wrap">
            {articles.length === 0 ? (
              <div className="empty-state">
                <div className="icon"><_I.search size={22} /></div>
                <div style={{ marginBottom: 4, color: "var(--ink-2)", fontWeight: 500 }}>
                  {empty || "No essays yet"}
                </div>
                <div style={{ fontSize: 14 }}>We're working on it — check back soon.</div>
              </div>
            ) : (
              <div className="latest-grid">
                {articles.map(a => <ArticleCard key={a.id} a={a} />)}
              </div>
            )}
          </div>
        </section>
      </main>
      <ContactCTA />
      <Footer />
      <SearchOverlay open={searchOpen} onClose={() => setSearchOpen(false)} />
    </>
  );
}

Object.assign(window, {
  DPAvatar: Avatar,
  DPCardMeta: CardMeta,
  DPArticleCard: ArticleCard,
  DPBreadcrumbs: Breadcrumbs,
  DPHeader: Header,
  DPContactCTA: ContactCTA,
  DPFooter: Footer,
  DPSearchOverlay: SearchOverlay,
  DPListingPage: ListingPage,
});
