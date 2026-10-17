/* Homepage announcement carousel. Shared announcement data remains unchanged. */
(() => {
    const card = document.getElementById('profileAnnouncementPill');
    if (!card) return;
    const items = [...card.querySelectorAll('.announcement-item')];
    const counter = document.getElementById('announcementCounter');
    const slides = document.getElementById('announcementSlides');
    const pauseButton = document.getElementById('pauseAnnouncements');
    const dialog = document.getElementById('announcementDialog');
    const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
    let current = 0;
    let timer;
    let paused = reducedMotion.matches;
    let hovered = false;
    let focused = false;

    function syncTimer() {
        clearInterval(timer);
        const stopped = paused || hovered || focused || document.hidden || dialog.open;
        slides.setAttribute('aria-live', stopped ? 'polite' : 'off');
        if (!stopped && items.length > 1) timer = setInterval(() => show(current + 1), 7000);
        if (pauseButton) {
            pauseButton.setAttribute('aria-pressed', String(paused));
            pauseButton.setAttribute('aria-label', paused ? 'Play announcements' : 'Pause announcements');
            pauseButton.firstElementChild.textContent = paused ? '\u25b7' : '\u2161';
        }
    }

    function show(index) {
        current = (index + items.length) % items.length;
        items.forEach((item, i) => {
            const active = i === current;
            item.classList.toggle('active', active);
            item.inert = !active;
            item.setAttribute('aria-hidden', String(!active));
        });
        if (counter) counter.textContent = `${String(current + 1).padStart(2, '0')} / ${String(items.length).padStart(2, '0')}`;
    }

    document.getElementById('prevAnnouncement')?.addEventListener('click', () => { show(current - 1); syncTimer(); });
    document.getElementById('nextAnnouncement')?.addEventListener('click', () => { show(current + 1); syncTimer(); });
    pauseButton?.addEventListener('click', () => { paused = !paused; syncTimer(); });
    card.addEventListener('mouseenter', () => { hovered = true; syncTimer(); });
    card.addEventListener('mouseleave', () => { hovered = false; syncTimer(); });
    card.addEventListener('focusin', () => { focused = true; syncTimer(); });
    card.addEventListener('focusout', () => {
        // Wait for focus to settle before deciding whether it left the card.
        queueMicrotask(() => { focused = card.contains(document.activeElement); syncTimer(); });
    });
    document.addEventListener('visibilitychange', syncTimer);
    reducedMotion.addEventListener('change', () => { paused = reducedMotion.matches; syncTimer(); });

    let touchStart;
    card.addEventListener('touchstart', event => {
        if (event.target.closest('a, button') || event.touches.length !== 1) { touchStart = null; return; }
        touchStart = { x: event.touches[0].clientX, y: event.touches[0].clientY };
    }, { passive: true });
    card.addEventListener('touchend', event => {
        if (!touchStart) return;
        const dx = event.changedTouches[0].clientX - touchStart.x;
        const dy = event.changedTouches[0].clientY - touchStart.y;
        if (Math.abs(dx) > 45 && Math.abs(dx) > Math.abs(dy) * 1.5) {
            show(current + (dx < 0 ? 1 : -1));
            syncTimer();
        }
        touchStart = null;
    }, { passive: true });
    card.addEventListener('touchcancel', () => { touchStart = null; }, { passive: true });

    items.forEach(item => {
        item.querySelector('.announcement-expand').addEventListener('click', () => {
            dialog.querySelector('h2').textContent = item.querySelector('.announcement-pill-title').textContent;
            dialog.querySelector('.announcement-dialog-meta').textContent = [item.querySelector('.announcement-pill-badge')?.textContent.trim(), item.querySelector('.announcement-pill-date').textContent.trim()].filter(Boolean).join(' \u00b7 ');
            dialog.querySelector('.announcement-dialog-text').textContent = item.querySelector('.announcement-pill-text').textContent;
            dialog.showModal();
            syncTimer();
        });
    });
    dialog.querySelector('button').addEventListener('click', () => dialog.close());
    dialog.addEventListener('close', syncTimer);
    dialog.addEventListener('click', event => {
        const rect = dialog.getBoundingClientRect();
        if (event.target === dialog && (event.clientX < rect.left || event.clientX > rect.right || event.clientY < rect.top || event.clientY > rect.bottom)) dialog.close();
    });

    function checkOverflow() {
        items.forEach(item => {
            const clipped = ['.announcement-pill-title', '.announcement-pill-text', '.announcement-pill-badge', '.announcement-pill-date span'].some(selector => {
                const node = item.querySelector(selector);
                return node && (node.scrollHeight > node.clientHeight + 1 || node.scrollWidth > node.clientWidth + 1);
            });
            item.querySelector('.announcement-expand').hidden = !clipped;
        });
    }
    new ResizeObserver(checkOverflow).observe(card);
    document.fonts.ready.then(checkOverflow);
    checkOverflow();
    syncTimer();
})();
