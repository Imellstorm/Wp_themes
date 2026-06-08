document.addEventListener('DOMContentLoaded', function () {
    /* FAQ accordion */
    document.querySelectorAll('.faq-question').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = this.closest('.faq-item');
            var isActive = item.classList.contains('active');

            document.querySelectorAll('.faq-item.active').forEach(function (el) {
                el.classList.remove('active');
                el.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            });

            if (!isActive) {
                item.classList.add('active');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });

    /* Header full-width search bar */
    var searchToggle = document.querySelector('.header-search-toggle');
    var searchBar    = document.querySelector('.header-search-bar');
    var searchClose  = document.querySelector('.header-search-close');

    function openSearch() {
        if (!searchBar) return;
        searchBar.classList.add('open');
        if (searchToggle) searchToggle.setAttribute('aria-expanded', 'true');
        searchBar.setAttribute('aria-hidden', 'false');
        var input = searchBar.querySelector('.header-search-input');
        if (input) setTimeout(function () { input.focus(); }, 60);
    }

    function closeSearch() {
        if (!searchBar) return;
        searchBar.classList.remove('open');
        if (searchToggle) searchToggle.setAttribute('aria-expanded', 'false');
        searchBar.setAttribute('aria-hidden', 'true');
    }

    if (searchToggle && searchBar) {
        searchToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            if (searchBar.classList.contains('open')) closeSearch();
            else openSearch();
        });
    }

    if (searchClose) {
        searchClose.addEventListener('click', closeSearch);
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && searchBar && searchBar.classList.contains('open')) {
            closeSearch();
            if (searchToggle) searchToggle.focus();
        }
    });

    /* Main nav (WP menu) — mobile toggle */
    var navToggle = document.querySelector('.main-nav-toggle');
    var navInner  = document.querySelector('.main-nav-inner');

    if (navToggle && navInner) {
        navToggle.addEventListener('click', function () {
            var isOpen = navInner.classList.toggle('open');
            navToggle.setAttribute('aria-expanded', isOpen ? 'true' : 'false');
        });
    }
});
