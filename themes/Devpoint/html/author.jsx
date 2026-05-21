/* global window, React, ReactDOM */
// devpoint — author page (?id=<authorId>)
const I = window.Icons;
const ARTICLES = window.ARTICLES;
const AUTHORS = window.AUTHORS;
const initials = window.AUTHOR_INITIALS;

const params = new URLSearchParams(location.search);
const authorId = params.get("id");
const author = AUTHORS[authorId] || AUTHORS.ms;
const articles = ARTICLES.filter(a => a.author.id === author.id);

document.title = `${author.name} — devpoint`;

function AuthorHead() {
  return (
    <div className="author-head">
      <div className="author-head-avatar"
           style={{ background: author.color }}>
        {initials(author.name)}
      </div>
      <div className="author-head-body">
        <div className="eyebrow">{author.role}</div>
        <h1 className="reader-title">{author.name}</h1>
        <p className="reader-lead">{author.bio}</p>
        <div className="author-head-meta">
          <span>{articles.length} {articles.length === 1 ? "essay" : "essays"} published</span>
        </div>
      </div>
    </div>
  );
}

ReactDOM.createRoot(document.getElementById("root")).render(
  <window.DPListingPage
    breadcrumbs={[
      { label: "Home", href: "index.html" },
      { label: "Authors" },
      { label: author.name },
    ]}
    head={<AuthorHead />}
    articles={articles}
    empty={`${author.name} hasn't published yet`}
  />
);
