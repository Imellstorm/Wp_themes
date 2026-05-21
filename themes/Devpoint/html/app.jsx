/* global window, React, ReactDOM */
// devpoint — blog by OGD Solutions. Single CTA points to the main site.

const { useState, useEffect, useMemo, useRef, useCallback } = React;

const ARTICLES = window.ARTICLES;
const CATEGORIES = window.CATEGORIES;
const HERO = window.HERO;
const SAMPLE_BODY = window.SAMPLE_BODY;
const ThumbArt = window.ThumbArt;
const HeroArt = window.HeroArt;
const CatGlyph = window.CatGlyph;
const I = window.Icons;
const MAIN = window.MAIN_SITE_URL;
const MAIN_LABEL = window.MAIN_SITE_LABEL;
const initials = window.AUTHOR_INITIALS;

const TWEAK_DEFAULTS = /*EDITMODE-BEGIN*/{
  "featuredLayout": "cards"
}/*EDITMODE-END*/;

// ── Small bits ─────────────────────────────────────────────────────────────
function Avatar({ author, size = 22 }) {
  return (
    <span className="avatar" style={{
      background: author.color, width: size, height: size, fontSize: size * 0.45
    }}>{initials(author.name)}</span>
  );
}

function CardMeta({ a, showAuthor = true }) {
  return (
    <div className="card-meta">
      {showAuthor && (
        <a className="author-tag" href={`author.html?id=${a.author.id}`}
           onClick={(e) => e.stopPropagation()}>
          <Avatar author={a.author} />
          <span className="author-name">{a.author.name}</span>
        </a>
      )}
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

// ── Header ─────────────────────────────────────────────────────────────────
function Header({ onSearchOpen, scrolled }) {
  return (
    <div className={`hdr-shell${scrolled ? " scrolled" : ""}`}>
      <div className="wrap hdr hdr-simple">
        <a href="#" className="brand" aria-label="devpoint">
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

// ── Hero ───────────────────────────────────────────────────────────────────
function Hero() {
  return (
    <section className="hero">
      <div className="wrap">
        <div className="hero-grid">
          <div>
            <span className="hero-eyebrow">
              <span className="pill">{HERO.badge}</span>
              {HERO.badgeText}
            </span>
            <h1>
              Field notes on shipping <em>websites</em> and{" "}
              <span className="underline">apps that work.</span>
            </h1>
            <p className="lead">{HERO.lead}</p>
            <div className="hero-ctas">
              <a href="#all" className="btn btn-primary btn-lg"
                 onClick={(e) => {
                   e.preventDefault();
                   const el = document.getElementById("all");
                   if (el) window.scrollTo({ top: el.offsetTop - 80, behavior: "smooth" });
                 }}>
                Start reading <I.arrow size={16} />
              </a>
            </div>
            <div className="hero-meta">
              {HERO.stats.map(s => (
                <div key={s.l} className="stat">
                  <b>{s.n}</b>
                  <span>{s.l}</span>
                </div>
              ))}
            </div>
          </div>

          <div className="hero-art">
            <HeroArt />
            <div className="float-card fc-1">
              <span className="fc-dot" style={{ background: "#5D8A6A" }}>
                <I.spark size={16} />
              </span>
              <div>
                <div style={{ fontWeight: 600 }}>Most-read this month</div>
                <div style={{ color: "var(--ink-3)", fontSize: 11.5 }}>"What an MVP actually is"</div>
              </div>
            </div>
            <div className="float-card fc-2">
              <span className="fc-dot" style={{ background: "#D67A52" }}>
                <I.clock size={16} />
              </span>
              <div>
                <div style={{ fontWeight: 600 }}>New every Thursday</div>
                <div style={{ color: "var(--ink-3)", fontSize: 11.5 }}>One essay, no fluff</div>
              </div>
            </div>
            <div className="float-card fc-3" style={{ background: "#1E1916", color: "#FBF7EF", borderColor: "#1E1916" }}>
              <span className="fc-dot" style={{ background: "#F5C56B", color: "#1E1916" }}>
                <I.bookmark size={14} />
              </span>
              <div>
                <div style={{ fontWeight: 600 }}>5 categories</div>
                <div style={{ color: "rgba(251,247,239,0.55)", fontSize: 11.5 }}>Web · Mobile · Business · Process · Cases</div>
              </div>
            </div>
          </div>
        </div>
      </div>
    </section>
  );
}

// ── Featured ───────────────────────────────────────────────────────────────
function Featured({ articles, layout, onOpen }) {
  const featured = articles.filter(a => a.featured);

  if (layout === "list") {
    return (
      <section className="section">
        <div className="wrap">
          <div className="section-h">
            <div>
              <div className="eyebrow">Editor's picks</div>
              <h2>The four <em>most-read</em> essays</h2>
            </div>
            <div className="right"><span>Shared most often by our readers</span></div>
          </div>
          <div className="featured-list">
            {featured.map((a, i) => (
              <button key={a.id} className="row" onClick={() => onOpen(a)}>
                <span className="idx">0{i + 1}</span>
                <div className="lst-thumb-wrap"><ThumbArt {...a.thumb} /></div>
                <div className="lst-title">{a.title}</div>
                <div className="lst-meta">
                  <div>{a.cat.name}</div>
                  <div style={{ color: "var(--ink-3)", marginTop: 2, fontSize: 12 }}>
                    {a.date} · {a.read} min
                  </div>
                </div>
                <span className="lst-arrow"><I.arrow size={16} /></span>
              </button>
            ))}
          </div>
        </div>
      </section>
    );
  }

  if (layout === "collage") {
    return (
      <section className="section">
        <div className="wrap">
          <div className="section-h">
            <div>
              <div className="eyebrow">Editor's picks</div>
              <h2>The four <em>most-read</em> essays</h2>
            </div>
          </div>
          <div className="featured-collage">
            {featured.map(a => <ArticleCard key={a.id} a={a} onOpen={onOpen} />)}
          </div>
        </div>
      </section>
    );
  }

  return (
    <section className="section">
      <div className="wrap">
        <div className="section-h">
          <div>
            <div className="eyebrow">Editor's picks</div>
            <h2>The four <em>most-read</em> essays</h2>
          </div>
          <div className="right"><span>Hand-picked from the archive</span></div>
        </div>
        <div className="featured-cards">
          {featured.map(a => <ArticleCard key={a.id} a={a} onOpen={onOpen} />)}
        </div>
      </div>
    </section>
  );
}

// ── Latest ─────────────────────────────────────────────────────────────────
function Latest({ articles, onOpen, filter, setFilter }) {
  const [visible, setVisible] = useState(6);
  const all = articles;
  const filtered = filter === "all" ? all : all.filter(a => a.cat.id === filter);
  useEffect(() => { setVisible(6); }, [filter]);
  const shown = filtered.slice(0, visible);

  return (
    <section className="section" id="all">
      <div className="wrap">
        <div className="section-h">
          <div>
            <div className="eyebrow">Fresh off the press</div>
            <h2>Latest <em>essays</em></h2>
          </div>
        </div>
        <div className="filter-bar" role="tablist">
          <button className={`filter-chip${filter === "all" ? " active" : ""}`}
                  onClick={() => setFilter("all")}>
            All <span style={{ opacity: 0.6 }}>· {all.length}</span>
          </button>
          {CATEGORIES.map(c => (
            <button key={c.id}
                    className={`filter-chip${filter === c.id ? " active" : ""}`}
                    onClick={() => setFilter(c.id)}>
              {c.name}
            </button>
          ))}
        </div>

        {shown.length === 0 ? (
          <div className="empty-state">
            <div className="icon"><I.search size={22} /></div>
            <div style={{ marginBottom: 4, color: "var(--ink-2)", fontWeight: 500 }}>
              No essays in this category yet
            </div>
            <div style={{ fontSize: 14 }}>We're working on it — check back soon.</div>
          </div>
        ) : (
          <>
            <div className="latest-grid">
              {shown.map(a => <ArticleCard key={a.id} a={a} onOpen={onOpen} />)}
            </div>
            {visible < filtered.length && (
              <div className="load-more">
                <button className="btn btn-ghost btn-lg" onClick={() => setVisible(v => v + 6)}>
                  Load more essays <I.chevron size={16} style={{ transform: "rotate(90deg)" }} />
                </button>
              </div>
            )}
          </>
        )}
      </div>
    </section>
  );
}

// ── Categories ─────────────────────────────────────────────────────────────
function Categories({ onCategory }) {
  return (
    <section className="section">
      <div className="wrap">
        <div className="section-h">
          <div>
            <div className="eyebrow">Browse by topic</div>
            <h2>Pick a <em>thread</em> to follow</h2>
          </div>
        </div>
        <div className="cat-grid">
          {CATEGORIES.map(c => (
            <button key={c.id} className="cat-tile" onClick={() => onCategory(c.id)}>
              <CatGlyph name={c.glyph} color={c.color} />
              <div>
                <div className="cat-name">{c.name}</div>
                <div className="cat-count"><span>{c.count} essays</span></div>
              </div>
              <span className="cat-arrow"><I.arrow size={14} /></span>
            </button>
          ))}
        </div>
      </div>
    </section>
  );
}

// ── Contact CTA ────────────────────────────────────────────────────────────
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

// ── Footer (with inline subscribe) ─────────────────────────────────────────
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
            <a href="#" className="brand">
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
              <li><a href="#all">Latest essays</a></li>
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

// ── Search overlay ─────────────────────────────────────────────────────────
function SearchOverlay({ open, onClose, onOpen }) {
  const [q, setQ] = useState("");
  const [focused, setFocused] = useState(0);
  const inputRef = useRef(null);

  useEffect(() => {
    if (open) {
      setTimeout(() => inputRef.current?.focus(), 30);
      setQ(""); setFocused(0);
    }
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

  const onKey = (e) => {
    if (e.key === "Escape") onClose();
    else if (e.key === "ArrowDown") { e.preventDefault(); setFocused(f => Math.min(f + 1, results.length - 1)); }
    else if (e.key === "ArrowUp")   { e.preventDefault(); setFocused(f => Math.max(f - 1, 0)); }
    else if (e.key === "Enter")     { e.preventDefault(); if (results[focused]) { onOpen(results[focused]); onClose(); } }
  };

  if (!open) return null;

  const hl = (text, q) => {
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
          {results.length === 0 ? (
            <div style={{ padding: "20px 16px", color: "var(--ink-3)" }}>
              No matches for "{q}". Try a broader term.
            </div>
          ) : results.map((a, i) => (
            <button key={a.id}
                    className={`search-result${i === focused ? " focused" : ""}`}
                    onMouseEnter={() => setFocused(i)}
                    onClick={() => { onOpen(a); onClose(); }}>
              <div className="sr-thumb"><ThumbArt {...a.thumb} /></div>
              <div className="sr-body">
                <div className="sr-title">{hl(a.title, q)}</div>
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

// ── Article reader ─────────────────────────────────────────────────────────
function Reader({ article, onClose, onOpen }) {
  const [progress, setProgress] = useState(0);
  const ref = useRef(null);

  useEffect(() => {
    const onScroll = () => {
      const el = ref.current; if (!el) return;
      const max = el.scrollHeight - el.clientHeight;
      setProgress(max > 0 ? (el.scrollTop / max) * 100 : 0);
    };
    const el = ref.current;
    el?.addEventListener("scroll", onScroll);
    return () => el?.removeEventListener("scroll", onScroll);
  }, [article]);

  useEffect(() => {
    if (!article) return;
    const onKey = (e) => { if (e.key === "Escape") onClose(); };
    document.body.style.overflow = "hidden";
    window.addEventListener("keydown", onKey);
    return () => {
      document.body.style.overflow = "";
      window.removeEventListener("keydown", onKey);
    };
  }, [article, onClose]);

  if (!article) return null;

  const a = article;
  const related = ARTICLES.filter(x => x.id !== a.id && x.cat.id === a.cat.id).slice(0, 3);

  return (
    <div className="reader" ref={ref}>
      <div className="reader-topbar">
        <div className="wrap reader-topbar-inner">
          <button className="back" onClick={onClose}>
            <I.chevron size={14} style={{ transform: "rotate(180deg)" }} /> Back to blog
          </button>
          <div style={{ fontSize: 13, color: "var(--ink-3)" }}>
            {a.cat.name}
          </div>
          <div className="reader-progress" style={{ width: `${progress}%` }} />
        </div>
      </div>
      <div className="reader-body">
        <div className="reader-cat">{a.cat.name}</div>
        <h1 className="reader-title">{a.title}</h1>
        <p className="reader-lead">{a.excerpt}</p>
        <div className="reader-byline">
          <Avatar author={a.author} size={40} />
          <div>
            <div><b>{a.author.name}</b> · <span style={{ color: "var(--ink-3)" }}>{a.author.role}</span></div>
            <div style={{ color: "var(--ink-3)", fontSize: 13 }}>
              {a.date} <span className="reader-meta-sep">·</span> {a.read} min read
            </div>
          </div>
          <div style={{ marginLeft: "auto", display: "flex", gap: 8 }}>
            <button className="icon-btn" aria-label="Bookmark"><I.bookmark size={16} /></button>
          </div>
        </div>
        <div className="reader-hero"><ThumbArt {...a.thumb} /></div>
        <div className="reader-content">
          {SAMPLE_BODY.map((b, i) => {
            if (b.type === "p")     return <p key={i}>{b.text}</p>;
            if (b.type === "h2")    return <h2 key={i}>{b.text}</h2>;
            if (b.type === "quote") return <blockquote key={i}>{b.text}</blockquote>;
            if (b.type === "ul")    return <ul key={i}>{b.items.map((it, j) => <li key={j}>{it}</li>)}</ul>;
            return null;
          })}
        </div>
        <div className="reader-cta">
          <h3>Enjoyed this essay?</h3>
          <p>devpoint is published by the team at OGD Solutions, where we design and build sites and apps for a living.</p>
        </div>

        {related.length > 0 && (
          <div style={{ marginTop: 72 }}>
            <div className="section-h" style={{ marginBottom: 20 }}>
              <div>
                <div className="eyebrow">Keep reading</div>
                <h2 style={{ fontSize: 32 }}>More in <em>{a.cat.name}</em></h2>
              </div>
            </div>
            <div className="latest-grid">
              {related.map(r => <ArticleCard key={r.id} a={r} onOpen={onOpen} />)}
            </div>
          </div>
        )}
      </div>
    </div>
  );
}

// ── Root ───────────────────────────────────────────────────────────────────
function App() {
  const [t, setTweak] = window.useTweaks(TWEAK_DEFAULTS);
  const [searchOpen, setSearchOpen] = useState(false);
  const [filter, setFilter] = useState("all");
  const [scrolled, setScrolled] = useState(false);

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

  const openArticle = useCallback((a) => {
    location.href = `post.html?id=${a.id}`;
  }, []);
  const goToCategory = useCallback((id) => {
    if (id === "all") {
      setFilter("all");
      setTimeout(() => {
        const el = document.getElementById("all");
        if (el) window.scrollTo({ top: el.offsetTop - 80, behavior: "smooth" });
      }, 60);
    } else {
      location.href = `category.html?id=${id}`;
    }
  }, []);

  return (
    <>
      <Header
        scrolled={scrolled}
        current={filter}
        onSearchOpen={() => setSearchOpen(true)}
      />
      <main>
        <Featured articles={ARTICLES} layout={t.featuredLayout} onOpen={openArticle} />
        <Categories onCategory={goToCategory} />
        <Latest articles={ARTICLES} onOpen={openArticle} filter={filter} setFilter={setFilter} />
        <ContactCTA />
      </main>
      <Footer />

      <SearchOverlay open={searchOpen} onClose={() => setSearchOpen(false)} onOpen={openArticle} />

      <window.TweaksPanel title="Tweaks">
        <window.TweakSection label="Featured layout">
          <window.TweakRadio
            label="Style"
            value={t.featuredLayout}
            options={["cards", "list", "collage"]}
            onChange={(v) => setTweak("featuredLayout", v)}
          />
        </window.TweakSection>
      </window.TweaksPanel>
    </>
  );
}

ReactDOM.createRoot(document.getElementById("root")).render(<App />);
