/* global window, React, ReactDOM */
// devpoint — category page (?id=<categoryId>)
const { useState } = React;
const I = window.Icons;
const ARTICLES = window.ARTICLES;
const CATEGORIES = window.CATEGORIES;
const CatGlyph = window.CatGlyph;

const params = new URLSearchParams(location.search);
const catId = params.get("id");
const cat = CATEGORIES.find(c => c.id === catId) || CATEGORIES[0];
const articles = ARTICLES.filter(a => a.cat.id === cat.id);

document.title = `${cat.name} — devpoint`;

function CategoryHead() {
  return (
    <div className="cat-head">
      <div className="cat-head-glyph" style={{ background: cat.color }}>
        {React.createElement(I[cat.glyph], { size: 30, stroke: 1.6 })}
      </div>
      <h1 className="reader-title">{cat.name}</h1>
      <p className="reader-lead">
        {articles.length} {articles.length === 1 ? "essay" : "essays"} on {cat.name.toLowerCase()} — every piece written by the team at OGD Solutions, based on real client work.
      </p>
      <div className="cat-head-meta">
        {CATEGORIES.filter(c => c.id !== cat.id).map(c => (
          <a key={c.id} href={`category.html?id=${c.id}`} className="cat-pill">
            {c.name}
          </a>
        ))}
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById("root")).render(
  <window.DPListingPage
    breadcrumbs={[
      { label: "Home", href: "index.html" },
      { label: "Categories", href: "index.html#all" },
      { label: cat.name },
    ]}
    head={<CategoryHead />}
    articles={articles}
    empty={`No essays in ${cat.name} yet`}
  />
);
