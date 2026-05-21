/* global window, React */
// Icons + procedural thumbnails for OGD Solutions blog.

const Icon = ({ d, size = 18, stroke = 1.7, fill = "none" }) =>
  React.createElement("svg", {
    width: size, height: size, viewBox: "0 0 24 24",
    fill, stroke: "currentColor", strokeWidth: stroke,
    strokeLinecap: "round", strokeLinejoin: "round",
  }, React.createElement("path", { d }));

window.Icons = {
  search:   (p) => <Icon {...p} d="M11 19a8 8 0 1 1 0-16 8 8 0 0 1 0 16ZM21 21l-4.3-4.3" />,
  menu:     (p) => <Icon {...p} d="M4 7h16M4 12h16M4 17h16" />,
  close:    (p) => <Icon {...p} d="M6 6l12 12M18 6L6 18" />,
  arrow:    (p) => <Icon {...p} d="M5 12h14M13 6l6 6-6 6" />,
  arrowUp:  (p) => <Icon {...p} d="M7 17L17 7M9 7h8v8" />,
  chevron:  (p) => <Icon {...p} d="M9 6l6 6-6 6" />,
  check:    (p) => <Icon {...p} d="M5 12l5 5L20 7" />,
  spark:    (p) => <Icon {...p} d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l3 3M15 15l3 3M6 18l3-3M15 9l3-3" />,
  globe:    (p) => <Icon {...p} d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20ZM2 12h20M12 2c2.5 3 4 6.5 4 10s-1.5 7-4 10c-2.5-3-4-6.5-4-10s1.5-7 4-10Z" />,
  phone:    (p) => <Icon {...p} d="M7 2h10a2 2 0 0 1 2 2v16a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2ZM11 18h2" />,
  briefcase:(p) => <Icon {...p} d="M3 8h18v12H3zM9 8V5a2 2 0 0 1 2-2h2a2 2 0 0 1 2 2v3M3 13h18" />,
  clipboard:(p) => <Icon {...p} d="M9 4h6a1 1 0 0 1 1 1v1h3v15H5V6h3V5a1 1 0 0 1 1-1ZM9 11h6M9 15h4" />,
  trophy:   (p) => <Icon {...p} d="M7 4h10v4a5 5 0 0 1-10 0V4ZM3 4h4v3a3 3 0 0 1-3 3M21 4h-4v3a3 3 0 0 0 3 3M9 14h6v3H9zM7 21h10" />,
  bookmark: (p) => <Icon {...p} d="M6 3h12v18l-6-4-6 4V3Z" />,
  clock:    (p) => <Icon {...p} d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20ZM12 7v5l3 2" />,
  twitter:  (p) => <Icon {...p} d="M22 5.8a8.2 8.2 0 0 1-2.4.7 4.2 4.2 0 0 0 1.8-2.3 8.2 8.2 0 0 1-2.6 1A4.1 4.1 0 0 0 11.5 9a11.7 11.7 0 0 1-8.5-4.3 4.1 4.1 0 0 0 1.3 5.5A4 4 0 0 1 2.4 9v.1A4.1 4.1 0 0 0 5.7 13a4.1 4.1 0 0 1-1.9.1 4.1 4.1 0 0 0 3.8 2.9A8.3 8.3 0 0 1 2 17.6 11.7 11.7 0 0 0 8.3 19.5c7.5 0 11.7-6.3 11.7-11.7v-.5A8.4 8.4 0 0 0 22 5.8Z" fill="currentColor" stroke="none" />,
  linkedin: (p) => <Icon {...p} d="M5 4a2 2 0 1 0 0-4 2 2 0 0 0 0 4ZM3 7h4v14H3zM10 7h4v2a4 4 0 0 1 7 3v9h-4v-9a2 2 0 0 0-4 0v9h-4V7Z" fill="currentColor" stroke="none" />,
  github:   (p) => <Icon {...p} d="M12 2a10 10 0 0 0-3.2 19.5c.5.1.7-.2.7-.5v-2c-2.8.6-3.4-1.2-3.4-1.2-.5-1.2-1.1-1.5-1.1-1.5-.9-.6.1-.6.1-.6 1 .1 1.5 1 1.5 1 .9 1.5 2.3 1.1 2.9.9.1-.7.4-1.1.6-1.4-2.2-.2-4.6-1.1-4.6-5 0-1.1.4-2 1-2.7-.1-.3-.4-1.3.1-2.7 0 0 .8-.3 2.7 1a9.5 9.5 0 0 1 5 0c1.9-1.3 2.7-1 2.7-1 .5 1.4.2 2.4.1 2.7.6.7 1 1.6 1 2.7 0 3.9-2.4 4.8-4.6 5 .4.3.7.9.7 1.8v2.7c0 .3.2.6.7.5A10 10 0 0 0 12 2Z" fill="currentColor" stroke="none" />,
  insta:    (p) => <Icon {...p} d="M7 2h10a5 5 0 0 1 5 5v10a5 5 0 0 1-5 5H7a5 5 0 0 1-5-5V7a5 5 0 0 1 5-5ZM12 8a4 4 0 1 0 0 8 4 4 0 0 0 0-8ZM17.5 5.5h.01" />,
  wa:       (p) => <Icon {...p} d="M20 12a8 8 0 1 1-15.3 3.2L4 20l4.9-1.3A8 8 0 0 1 20 12ZM9 9c.2-.5.5-.5.8-.5.2 0 .5 0 .8.4l.6 1.3a.6.6 0 0 1 0 .6l-.4.5c-.1.1-.2.3-.1.5.3.7 1 1.4 1.7 1.7.2.1.4 0 .5-.1l.5-.5c.1-.1.4-.2.6-.1l1.2.6c.5.2.5.5.5.7 0 .3-.3 1.1-.5 1.3-.3.3-1.4.7-2.4.3-.9-.4-2-1.2-2.7-1.9-.7-.7-1.5-1.8-1.9-2.7-.4-1 0-2 .3-2.4Z" />,
  sun:      (p) => <Icon {...p} d="M12 7a5 5 0 1 0 0 10 5 5 0 0 0 0-10ZM12 2v2M12 20v2M4.2 4.2l1.4 1.4M18.4 18.4l1.4 1.4M2 12h2M20 12h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4" />,
};

// ── Procedural thumbnail art ───────────────────────────────────────────────
window.ThumbArt = function ThumbArt({ style, tones }) {
  const [c1, c2, c3] = tones;
  const common = {
    width: "100%", height: "100%", viewBox: "0 0 200 160",
    preserveAspectRatio: "xMidYMid slice",
  };

  switch (style) {
    case "stack":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect width="200" height="160" fill={c2} />
            <circle cx="160" cy="-10" r="80" fill={c3} opacity="0.7" />
            <rect x="30"  y="40" width="120" height="14" rx="3" fill={c1} />
            <rect x="30"  y="62" width="90"  height="14" rx="3" fill={c1} opacity="0.55" />
            <rect x="30"  y="84" width="105" height="14" rx="3" fill={c1} opacity="0.3"  />
            <circle cx="155" cy="110" r="22" fill={c1} />
          </svg>
        </div>
      );
    case "phone":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect width="200" height="160" fill={c2} />
            <circle cx="40" cy="135" r="50" fill={c3} opacity="0.6" />
            <rect x="78" y="22" width="64" height="120" rx="12" fill={c1} />
            <rect x="84" y="32" width="52" height="78" rx="4" fill={c2} opacity="0.95" />
            <rect x="92" y="44" width="36" height="6" rx="2" fill={c1} opacity="0.5" />
            <rect x="92" y="56" width="28" height="6" rx="2" fill={c1} opacity="0.25" />
            <rect x="92" y="76" width="36" height="20" rx="3" fill={c1} opacity="0.15" />
            <circle cx="110" cy="125" r="6" fill={c2} />
          </svg>
        </div>
      );
    case "rings":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect width="200" height="160" fill={c2} />
            <circle cx="100" cy="80" r="62" fill="none" stroke={c1} strokeWidth="2" opacity="0.3" />
            <circle cx="100" cy="80" r="44" fill="none" stroke={c1} strokeWidth="2" opacity="0.5" />
            <circle cx="100" cy="80" r="26" fill={c1} />
            <circle cx="100" cy="80" r="10" fill={c3} />
          </svg>
        </div>
      );
    case "grid":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect width="200" height="160" fill={c2} />
            {[0,1,2,3].map(i => [0,1,2,3,4].map(j => (
              <rect key={`${i}-${j}`}
                    x={20 + j*34} y={18 + i*32}
                    width="28" height="26" rx="4"
                    fill={c1}
                    opacity={((i+j) % 3 === 0) ? 0.85 : 0.18} />
            )))}
          </svg>
        </div>
      );
    case "circles":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect width="200" height="160" fill={c2} />
            <circle cx="60"  cy="60"  r="50" fill={c1} opacity="0.85" />
            <circle cx="140" cy="100" r="36" fill={c3} />
            <circle cx="120" cy="50"  r="14" fill={c1} opacity="0.5" />
            <circle cx="170" cy="140" r="10" fill={c1} opacity="0.4" />
          </svg>
        </div>
      );
    case "layers":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect width="200" height="160" fill={c2} />
            <rect x="36" y="36" width="128" height="92" rx="6" fill={c1} opacity="0.25" />
            <rect x="48" y="52" width="128" height="92" rx="6" fill={c1} opacity="0.55" />
            <rect x="60" y="68" width="128" height="92" rx="6" fill={c1} />
          </svg>
        </div>
      );
    case "bars":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect width="200" height="160" fill={c2} />
            {[44, 72, 56, 96, 64, 110, 80].map((h, i) => (
              <rect key={i} x={24 + i*22} y={140 - h} width="14" height={h} rx="3"
                    fill={c1} opacity={0.45 + (i%3)*0.18} />
            ))}
            <path d="M22 90 Q60 60, 100 70 T180 40" fill="none" stroke={c1} strokeWidth="2" strokeLinecap="round" />
          </svg>
        </div>
      );
    case "scatter":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect width="200" height="160" fill={c2} />
            {Array.from({ length: 18 }, (_, i) => {
              const x = 18 + (i * 37) % 170;
              const y = 22 + ((i * 53) % 110);
              const r = 4 + (i % 4) * 3;
              return <circle key={i} cx={x} cy={y} r={r} fill={c1} opacity={0.25 + (i%3)*0.25} />;
            })}
            <circle cx="120" cy="80" r="28" fill={c3} opacity="0.85" />
          </svg>
        </div>
      );
    case "tree":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect width="200" height="160" fill={c2} />
            <line x1="100" y1="30" x2="60"  y2="76" stroke={c1} strokeWidth="2" opacity="0.6" />
            <line x1="100" y1="30" x2="140" y2="76" stroke={c1} strokeWidth="2" opacity="0.6" />
            <line x1="60"  y1="76" x2="40"  y2="120" stroke={c1} strokeWidth="2" opacity="0.45" />
            <line x1="60"  y1="76" x2="80"  y2="120" stroke={c1} strokeWidth="2" opacity="0.45" />
            <line x1="140" y1="76" x2="120" y2="120" stroke={c1} strokeWidth="2" opacity="0.45" />
            <line x1="140" y1="76" x2="160" y2="120" stroke={c1} strokeWidth="2" opacity="0.45" />
            <circle cx="100" cy="30" r="14" fill={c1} />
            <circle cx="60"  cy="76" r="10" fill={c1} opacity="0.7" />
            <circle cx="140" cy="76" r="10" fill={c1} opacity="0.7" />
            <circle cx="40"  cy="120" r="7"  fill={c3} />
            <circle cx="80"  cy="120" r="7"  fill={c3} />
            <circle cx="120" cy="120" r="7"  fill={c3} />
            <circle cx="160" cy="120" r="7"  fill={c3} />
          </svg>
        </div>
      );
    case "split":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect x="0" y="0" width="100" height="160" fill={c1} />
            <rect x="100" y="0" width="100" height="160" fill={c2} />
            <circle cx="50" cy="80" r="26" fill={c3} />
            <circle cx="150" cy="80" r="26" fill={c1} />
            <line x1="100" y1="0" x2="100" y2="160" stroke={c3} strokeWidth="2" />
          </svg>
        </div>
      );
    case "chart":
      return (
        <div className="thumb-art" style={{ background: c2 }}>
          <svg {...common}>
            <rect width="200" height="160" fill={c2} />
            <path d="M20 130 L60 110 L100 90 L140 60 L180 30"
                  fill="none" stroke={c1} strokeWidth="3" strokeLinecap="round" />
            <path d="M20 130 L60 110 L100 90 L140 60 L180 30 L180 140 L20 140 Z"
                  fill={c1} opacity="0.15" />
            {[20,60,100,140,180].map((x, i) => {
              const ys = [130,110,90,60,30];
              return <circle key={i} cx={x} cy={ys[i]} r="4" fill={c1} />;
            })}
          </svg>
        </div>
      );
    default:
      return <div className="thumb-art" style={{ background: c1 }} />;
  }
};

// ── Hero illustration ──────────────────────────────────────────────────────
window.HeroArt = function HeroArt() {
  return (
    <svg viewBox="0 0 600 600" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
      <defs>
        <radialGradient id="hg1" cx="40%" cy="35%" r="60%">
          <stop offset="0%"   stopColor="#F4D8C8" />
          <stop offset="100%" stopColor="#D67A52" />
        </radialGradient>
        <linearGradient id="hg2" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" stopColor="#5D8A6A" />
          <stop offset="100%" stopColor="#3F6E81" />
        </linearGradient>
        <pattern id="dots" x="0" y="0" width="14" height="14" patternUnits="userSpaceOnUse">
          <circle cx="2" cy="2" r="1.2" fill="#1E1916" opacity="0.18" />
        </pattern>
      </defs>

      {/* big sun */}
      <circle cx="380" cy="220" r="160" fill="url(#hg1)" />

      {/* dotted background blob */}
      <path d="M120,420 Q60,360 110,300 Q150,250 100,200 Q60,150 130,110 Q200,80 240,150 Q280,220 320,200 Q400,180 380,260 Q360,360 280,400 Q200,440 120,420 Z"
            fill="url(#dots)" />

      {/* sage blob */}
      <path d="M100,440 Q200,520 320,500 Q440,480 460,400 Q480,330 400,330 Q320,330 280,400 Q240,470 160,470 Q120,470 100,440 Z"
            fill="url(#hg2)" opacity="0.85" />

      {/* butter ring */}
      <circle cx="180" cy="180" r="80" fill="none" stroke="#F5C56B" strokeWidth="20" strokeDasharray="180 50" strokeLinecap="round" transform="rotate(-30 180 180)" />

      {/* plum half-disk */}
      <path d="M460,440 a90,90 0 0 1 -180,0 z" fill="#8B5E83" />

      {/* small accents */}
      <circle cx="120" cy="120" r="10" fill="#1E1916" />
      <circle cx="500" cy="120" r="14" fill="#F5C56B" />
      <rect x="460" y="320" width="60" height="60" rx="14" fill="#1E1916" transform="rotate(15 490 350)" />

      {/* sparkle */}
      <g transform="translate(330,140)">
        <path d="M0,-20 L4,-4 L20,0 L4,4 L0,20 L-4,4 L-20,0 L-4,-4 Z" fill="#FBF7EF" stroke="#1E1916" strokeWidth="2" />
      </g>
    </svg>
  );
};

window.CatGlyph = function CatGlyph({ name, color }) {
  const I = window.Icons[name];
  return (
    <div className="cat-glyph" style={{ background: color }}>
      {I && <I size={20} stroke={1.8} />}
    </div>
  );
};
