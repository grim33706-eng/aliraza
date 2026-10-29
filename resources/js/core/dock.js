// Floating contact menu: the toggle fans out every contact channel.
export function initDock() {
    const dock = document.querySelector('[data-dock]');
    const toggle = dock?.querySelector('[data-dock-toggle]');
    if (!dock || !toggle) return;

    const setOpen = (open) => {
        dock.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Close contact links' : 'Open contact links');
    };

    toggle.addEventListener('click', () => setOpen(!dock.classList.contains('is-open')));

    // Close on Escape (returning focus) or on a click anywhere else.
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && dock.classList.contains('is-open')) {
            setOpen(false);
            toggle.focus();
        }
    });
    document.addEventListener('click', (e) => {
        if (dock.classList.contains('is-open') && !dock.contains(e.target)) setOpen(false);
    });
}
