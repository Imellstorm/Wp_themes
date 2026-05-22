/* devpoint — small runtime behaviors.
   Mirrors the bits of app.jsx / post.jsx / chrome.jsx that need to be live
   in the browser: sticky-header border, reading progress, search overlay,
   smooth scroll, newsletter form (client-side validation only). */

(function () {
	'use strict';

	const ready = (fn) => {
		if (document.readyState !== 'loading') fn();
		else document.addEventListener('DOMContentLoaded', fn);
	};

	ready(() => {
		stickyHeader();
		postProgress();
		smoothScroll();
		searchOverlay();
		subscribeForm();
	});

	/* ── Sticky header border on scroll ───────────────────────────────── */
	function stickyHeader() {
		const hdr = document.querySelector('[data-hdr]');
		if (!hdr) return;
		const onScroll = () => hdr.classList.toggle('scrolled', window.scrollY > 8);
		onScroll();
		window.addEventListener('scroll', onScroll, { passive: true });
	}

	/* ── Reading progress on single posts ─────────────────────────────── */
	function postProgress() {
		const bar = document.querySelector('[data-post-progress]');
		if (!bar) return;
		const update = () => {
			const max = document.documentElement.scrollHeight - window.innerHeight;
			const pct = max > 0 ? (window.scrollY / max) * 100 : 0;
			bar.style.width = pct + '%';
		};
		update();
		window.addEventListener('scroll', update, { passive: true });
		window.addEventListener('resize', update);
	}

	/* ── Smooth-scroll CTAs to in-page anchors (e.g. #all) ────────────── */
	function smoothScroll() {
		document.querySelectorAll('a[data-scroll-target], a[href^="#"]').forEach(a => {
			a.addEventListener('click', (e) => {
				const id = a.dataset.scrollTarget || (a.getAttribute('href') || '').slice(1);
				if (!id) return;
				const el = document.getElementById(id);
				if (!el) return;
				e.preventDefault();
				window.scrollTo({ top: el.offsetTop - 80, behavior: 'smooth' });
				history.replaceState(null, '', '#' + id);
			});
		});
	}

	/* ── Search overlay ───────────────────────────────────────────────── */
	function searchOverlay() {
		const overlay = document.querySelector('[data-search-overlay]');
		const panel   = document.querySelector('[data-search-panel]');
		const input   = document.querySelector('[data-search-input]');
		const list    = document.querySelector('[data-search-results]');
		if (!overlay || !input || !list) return;

		const cfg = window.DevpointSearch || {};
		const strings = cfg.strings || {};
		let focused = 0;
		let last = [];
		let debounceTimer = null;

		const open = () => {
			overlay.removeAttribute('hidden');
			setTimeout(() => input.focus(), 30);
			input.value = '';
			render('');
		};
		const close = () => {
			overlay.setAttribute('hidden', '');
		};
		const goTo = (a) => {
			if (a && a.link) location.href = a.link;
		};

		document.querySelectorAll('[data-search-open]').forEach(btn =>
			btn.addEventListener('click', open)
		);
		overlay.addEventListener('click', (e) => { if (e.target === overlay) close(); });
		document.addEventListener('keydown', (e) => {
			if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
				e.preventDefault();
				open();
			} else if (e.key === 'Escape' && !overlay.hasAttribute('hidden')) {
				close();
			} else if (!overlay.hasAttribute('hidden')) {
				if (e.key === 'ArrowDown') {
					e.preventDefault();
					focused = Math.min(focused + 1, last.length - 1);
					highlight();
				} else if (e.key === 'ArrowUp') {
					e.preventDefault();
					focused = Math.max(focused - 1, 0);
					highlight();
				} else if (e.key === 'Enter') {
					e.preventDefault();
					goTo(last[focused]);
				}
			}
		});

		input.addEventListener('input', () => {
			clearTimeout(debounceTimer);
			const q = input.value.trim();
			debounceTimer = setTimeout(() => render(q), 200);
		});

		function highlight() {
			[...list.querySelectorAll('.search-result')].forEach((el, i) =>
				el.classList.toggle('focused', i === focused)
			);
		}

		function highlightTerm(text, q) {
			if (!q) return escapeHtml(text);
			const i = text.toLowerCase().indexOf(q.toLowerCase());
			if (i < 0) return escapeHtml(text);
			return (
				escapeHtml(text.slice(0, i)) +
				'<mark>' + escapeHtml(text.slice(i, i + q.length)) + '</mark>' +
				escapeHtml(text.slice(i + q.length))
			);
		}

		function escapeHtml(s) {
			return String(s)
				.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;')
				.replace(/"/g, '&quot;').replace(/'/g, '&#39;');
		}

		async function render(q) {
			if (!cfg.restUrl) {
				list.innerHTML = '';
				return;
			}
			const url = new URL(cfg.restUrl);
			url.searchParams.set('per_page', '8');
			url.searchParams.set('_fields', 'id,title,excerpt,link,date');
			if (q) url.searchParams.set('search', q);
			else   url.searchParams.set('orderby', 'date');

			try {
				const res = await fetch(url.toString(), { headers: { 'Accept': 'application/json' } });
				if (!res.ok) throw new Error('search request failed');
				const data = await res.json();
				last = data;
				focused = 0;
				renderList(q, data);
			} catch (_) {
				list.innerHTML = '<div style="padding:20px 16px;color:var(--ink-3);">' +
					escapeHtml(strings.noResults || 'No results.') + '</div>';
			}
		}

		function renderList(q, data) {
			const header = q
				? (data.length === 1 ? (strings.resultOne || '1 result')
				                     : (strings.resultMany || '%d results').replace('%d', data.length))
				: (strings.suggested || 'Suggested for you');

			if (!data.length) {
				list.innerHTML =
					'<div class="search-section-label">' + escapeHtml(header) + '</div>' +
					'<div style="padding:20px 16px;color:var(--ink-3);">' +
					escapeHtml(strings.noResults || 'No matches.') + '</div>';
				return;
			}

			let html = '<div class="search-section-label">' + escapeHtml(header) + '</div>';
			data.forEach((a, i) => {
				const title = (a.title && a.title.rendered) || '';
				const exc   = (a.excerpt && a.excerpt.rendered) || '';
				const plain = exc.replace(/<[^>]+>/g, '').trim().slice(0, 80);
				html += '<a class="search-result' + (i === 0 ? ' focused' : '') + '" href="' + escapeHtml(a.link) + '">' +
					'<div class="sr-body">' +
						'<div class="sr-title">' + highlightTerm(title.replace(/<[^>]+>/g, ''), q) + '</div>' +
						'<div class="sr-meta"><span>' + escapeHtml(plain) + '</span></div>' +
					'</div>' +
				'</a>';
			});
			list.innerHTML = html;

			[...list.querySelectorAll('.search-result')].forEach((el, i) => {
				el.addEventListener('mouseenter', () => { focused = i; highlight(); });
			});
		}
	}

	/* ── Footer subscribe form (client-side only) ─────────────────────── */
	function subscribeForm() {
		const form = document.querySelector('[data-subscribe-form]');
		if (!form) return;
		const ok  = form.parentElement.querySelector('.ftr-sub-success');
		const err = form.parentElement.querySelector('[data-subscribe-err]');

		form.addEventListener('submit', (e) => {
			e.preventDefault();
			const email = (form.querySelector('input[type=email]') || {}).value || '';
			if (!/^[^@\s]+@[^@\s]+\.[^@\s]+$/.test(email)) {
				if (err) {
					err.removeAttribute('hidden');
					err.textContent = 'Please enter a valid email address.';
				}
				return;
			}
			if (err) err.setAttribute('hidden', '');
			try { localStorage.setItem('devpoint-newsletter', email); } catch (_) {}
			form.setAttribute('hidden', '');
			if (ok) ok.removeAttribute('hidden');
		});
	}
})();
