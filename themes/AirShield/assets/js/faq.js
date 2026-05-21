document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.faq-question').forEach(function (btn) {
        btn.addEventListener('click', function () {
            var item = this.closest('.faq-item');
            var isActive = item.classList.contains('active');

            // Close all
            document.querySelectorAll('.faq-item.active').forEach(function (el) {
                el.classList.remove('active');
                el.querySelector('.faq-question').setAttribute('aria-expanded', 'false');
            });

            // Open clicked if it was closed
            if (!isActive) {
                item.classList.add('active');
                this.setAttribute('aria-expanded', 'true');
            }
        });
    });
});
