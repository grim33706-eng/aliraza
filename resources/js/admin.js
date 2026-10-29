// Admin panel behaviour: theme, sidebar, collapsible sections, repeaters,
// lists, media pickers, previews, unsaved-change warnings and toasts.

const $ = (sel, root = document) => root.querySelector(sel);
const $$ = (sel, root = document) => [...root.querySelectorAll(sel)];
const escapeHtml = (s) => s.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
const uid = () => `n${Date.now().toString(36)}${Math.random().toString(36).slice(2, 6)}`;

/* ---------- Theme (shared with the website) ---------- */
$('[data-theme-toggle]')?.addEventListener('click', () => {
    const root = document.documentElement;
    const next = root.dataset.theme === 'light' ? 'dark' : 'light';
    root.dataset.theme = next;
    try {
        localStorage.setItem('raza-theme', next);
    } catch {
        /* storage unavailable */
    }
});

/* ---------- Mobile sidebar ---------- */
const sidebarToggle = $('[data-sidebar-toggle]');
const setSidebar = (open) => {
    document.body.classList.toggle('sidebar-open', open);
    sidebarToggle?.setAttribute('aria-expanded', String(open));
};
sidebarToggle?.addEventListener('click', () => setSidebar(!document.body.classList.contains('sidebar-open')));
$('[data-sidebar-close]')?.addEventListener('click', () => setSidebar(false));
document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') {
        setSidebar(false);
        $$('.ad-menu[open]').forEach((m) => m.removeAttribute('open'));
    }
});
document.addEventListener('click', (e) => {
    $$('.ad-menu[open]').forEach((m) => !m.contains(e.target) && m.removeAttribute('open'));
});

/* ---------- Icons for toasts and dialogs (rendered server-side) ---------- */
const uiIcons = $('template[data-ui-icons]')?.content;
const icon = (name) => uiIcons?.querySelector(`[data-icon="${name}"]`)?.innerHTML || '';

/* ---------- Toasts ---------- */
// toast({ type: success|deleted|error|info, title, message, icon, action: { label, run }, key })
// A toast with the same `key` replaces the previous one instead of stacking.
const toastIcons = { success: 'check', deleted: 'trash', error: 'alert', info: 'sparkles' };
const toastStack = $('[data-toasts]');
function toast({ type = 'success', title, message = '', icon: iconName, action, key, life = action ? 7000 : 5000 }) {
    if (!toastStack) return;
    if (key) $$(`[data-toast-key="${key}"]`, toastStack).forEach((old) => old.remove());

    const el = document.createElement('div');
    el.className = `ad-toast ad-toast--${type}`;
    el.setAttribute('role', type === 'error' ? 'alert' : 'status');
    el.style.setProperty('--life', `${life}ms`);
    if (key) el.dataset.toastKey = key;
    el.innerHTML = `
        <span class="ad-toast__icon">${icon(iconName || toastIcons[type] || 'check')}</span>
        <div class="ad-toast__body">
            <strong class="ad-toast__title"></strong>
            ${message ? '<p class="ad-toast__text"></p>' : ''}
            ${action ? '<button type="button" class="ad-toast__action"></button>' : ''}
        </div>
        <button type="button" class="ad-toast__close" aria-label="Dismiss">${icon('close')}</button>
        <span class="ad-toast__bar"></span>`;
    $('.ad-toast__title', el).textContent = title;
    if (message) $('.ad-toast__text', el).textContent = message;

    const close = () => {
        if (el.classList.contains('is-leaving')) return;
        el.classList.add('is-leaving');
        setTimeout(() => el.remove(), 300);
    };
    if (action) {
        const btn = $('.ad-toast__action', el);
        btn.textContent = action.label;
        btn.addEventListener('click', () => {
            action.run();
            close();
        });
    }
    $('.ad-toast__close', el).addEventListener('click', close);
    // The timer bar drives the lifetime, so hovering (which pauses it) keeps the toast open.
    $('.ad-toast__bar', el).addEventListener('animationend', close);

    toastStack.prepend(el);
    $$('.ad-toast', toastStack).slice(4).forEach((old) => old.remove());
    return el;
}
try {
    JSON.parse($('[data-toast-data]')?.textContent || '[]').forEach((t) => toast(t));
} catch {
    /* malformed flash data */
}

/* ---------- Confirm dialog ---------- */
// Resolves true when confirmed; Escape, Cancel or a click outside resolve false.
function confirmDialog({ title = 'Are you sure?', message = '', confirm = 'Delete', tone = 'danger', icon: iconName = 'trash' }) {
    return new Promise((resolve) => {
        const dialog = document.createElement('dialog');
        dialog.className = `ad-dialog ad-dialog--${tone}`;
        dialog.setAttribute('aria-labelledby', 'ad-dialog-title');
        dialog.innerHTML = `
            <form method="dialog" class="ad-dialog__box">
                <span class="ad-dialog__icon">${icon(iconName)}</span>
                <h2 class="ad-dialog__title" id="ad-dialog-title"></h2>
                <p class="ad-dialog__text"></p>
                <div class="ad-dialog__actions">
                    <button value="cancel" class="ad-btn ad-btn--ghost">Cancel</button>
                    <button value="ok" class="ad-btn ad-dialog__confirm"></button>
                </div>
            </form>`;
        $('.ad-dialog__title', dialog).textContent = title;
        $('.ad-dialog__text', dialog).textContent = message;
        $('.ad-dialog__confirm', dialog).textContent = confirm;
        dialog.addEventListener('click', (e) => e.target === dialog && dialog.close('cancel'));
        dialog.addEventListener('close', () => {
            resolve(dialog.returnValue === 'ok');
            dialog.remove();
        });
        document.body.appendChild(dialog);
        dialog.showModal();
        $('button[value="cancel"]', dialog).focus(); // safe default for destructive actions
    });
}

/* ---------- Confirm before destructive submits ---------- */
document.addEventListener('submit', (e) => {
    const form = e.target;
    if (!form.dataset.confirm || form.dataset.confirmed) return;
    e.preventDefault();
    e.stopImmediatePropagation();
    confirmDialog({
        title: form.dataset.confirmTitle,
        message: form.dataset.confirm,
        confirm: form.dataset.confirmLabel,
        tone: form.dataset.confirmTone,
        icon: form.dataset.confirmIcon,
    }).then((ok) => {
        if (!ok) return;
        form.dataset.confirmed = '1';
        form.requestSubmit();
    });
}, true);

/* ---------- Password show/hide + rules ---------- */
$$('[data-password-toggle]').forEach((btn) => {
    btn.addEventListener('click', () => {
        const input = btn.parentElement.querySelector('input');
        const show = input.type === 'password';
        input.type = show ? 'text' : 'password';
        btn.setAttribute('aria-label', show ? 'Hide password' : 'Show password');
        btn.classList.toggle('is-on', show);
        btn.setAttribute('aria-pressed', String(show));
    });
});
const strength = $('[data-password-strength]');
const rules = $('[data-password-rules]');
if (strength && rules) {
    const tests = { length: (v) => v.length >= 8, letter: (v) => /[a-z]/i.test(v), number: (v) => /\d/.test(v) };
    strength.addEventListener('input', () => {
        $$('[data-rule]', rules).forEach((li) => li.classList.toggle('is-met', tests[li.dataset.rule](strength.value)));
    });
}

/* ---------- Collapsible page sections ---------- */
const openSection = (section, scroll = false) => {
    section.classList.add('is-open');
    $('[data-section-toggle]', section)?.setAttribute('aria-expanded', 'true');
    enhance(section);
    if (scroll) {
        section.scrollIntoView({ behavior: 'smooth', block: 'start' });
        section.classList.remove('is-flash');
        void section.offsetWidth;
        section.classList.add('is-flash');
    }
};
$$('[data-section]').forEach((section) => {
    $('[data-section-toggle]', section).addEventListener('click', () => {
        const open = !section.classList.contains('is-open');
        section.classList.toggle('is-open', open);
        $('[data-section-toggle]', section).setAttribute('aria-expanded', String(open));
        if (open) enhance(section);
    });
});
const fromHash = () => {
    const target = location.hash ? document.getElementById(location.hash.slice(1)) : null;
    if (target?.matches('[data-section]')) openSection(target, true);
};
window.addEventListener('hashchange', fromHash);
$$('[data-section-link]').forEach((link) => link.addEventListener('click', (e) => {
    e.preventDefault();
    const section = document.getElementById(`section-${link.dataset.sectionLink}`);
    history.replaceState(null, '', `#section-${link.dataset.sectionLink}`);
    openSection(section, true);
}));

// Highlight the section in view in the "On this page" list.
const tocLinks = $$('[data-section-link]');
if (tocLinks.length && 'IntersectionObserver' in window) {
    const io = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (!entry.isIntersecting) return;
            const index = entry.target.id.replace('section-', '');
            tocLinks.forEach((l) => l.classList.toggle('is-current', l.dataset.sectionLink === index));
        });
    }, { rootMargin: '-30% 0px -60% 0px' });
    $$('[data-section]').forEach((s) => io.observe(s));
}

/* ---------- Rich-text preview: *accent* and **bold** ---------- */
const renderRich = (text) => escapeHtml(text).replace(/\*\*(.+?)\*\*/g, '<strong>$1</strong>').replace(/\*(.+?)\*/g, '<em>$1</em>');
const updateRich = (textarea) => {
    const preview = textarea.parentElement.querySelector('[data-rich-preview]');
    if (preview) preview.innerHTML = /\*/.test(textarea.value) ? renderRich(textarea.value) : '';
};

/* ---------- Textarea auto-height ---------- */
const autosize = (el) => {
    if (!el.offsetParent) return; // hidden (closed section): sized when opened
    el.style.height = 'auto';
    el.style.height = `${el.scrollHeight + 2}px`;
};

/* ---------- Icon picker preview ---------- */
const iconLibrary = $('[data-icon-library]');
const updateIcon = (select) => {
    const preview = select.parentElement.querySelector('[data-icon-preview]');
    const art = iconLibrary?.querySelector(`[data-icon="${CSS.escape(select.value)}"]`);
    if (preview) preview.innerHTML = art ? art.innerHTML : '';
};

/* ---------- Repeater & list helpers ---------- */
const refreshRepeater = (repeater) => {
    const items = $$(':scope > [data-repeater-items] > [data-repeater-item]', repeater);
    items.forEach((item, i) => {
        const index = item.querySelector(':scope > .ad-repeater__head [data-item-index]');
        if (index) index.textContent = String(i + 1).padStart(2, '0');
    });
    const count = repeater.querySelector(':scope > .ad-repeater__label [data-repeater-count]');
    if (count) count.textContent = items.length;
};

// Repeater item title follows its label field (e.g. the service title).
const updateItemTitle = (input) => {
    const item = input.closest('[data-repeater-item]');
    const repeater = item?.closest('[data-repeater]');
    const field = repeater?.dataset.itemLabel;
    if (!field || !input.name.endsWith(`[${field}]`)) return;
    // Only the item's own field, not one inside a nested repeater.
    if (input.closest('[data-repeater-item]') !== item) return;
    const title = item.querySelector(':scope > .ad-repeater__head [data-item-title]');
    if (title) title.textContent = input.value.trim() || 'New item';
};

function enhance(root) {
    $$('textarea[data-autosize]', root).forEach(autosize);
    $$('textarea[data-rich]', root).forEach(updateRich);
    $$('select[data-icon-select]', root).forEach(updateIcon);
    $$('[data-repeater]', root).forEach(refreshRepeater);
}

const markDirty = (form) => {
    if (!form || !form.matches('[data-content-form]')) return;
    form.dataset.dirty = '1';
    const flag = form.querySelector('[data-dirty]');
    if (flag) flag.hidden = false;
};

document.addEventListener('input', (e) => {
    const el = e.target;
    if (el.matches('textarea[data-autosize]')) autosize(el);
    if (el.matches('textarea[data-rich]')) updateRich(el);
    if (el.matches('input, textarea')) updateItemTitle(el);
    markDirty(el.closest('form'));
});
document.addEventListener('change', (e) => {
    const el = e.target;
    if (el.matches('select[data-icon-select]')) updateIcon(el);
    markDirty(el.closest('form'));
});

// Take an element out of the page, with a toast that can put it back.
const removeWithUndo = (el, { title, message, onChange }) => {
    const parent = el.parentElement;
    const next = el.nextElementSibling;
    let undone = false;
    el.classList.add('is-removing');
    setTimeout(() => {
        if (undone) return;
        el.remove();
        el.classList.remove('is-removing');
        onChange();
    }, 220);
    toast({
        type: 'deleted',
        title,
        message,
        action: {
            label: 'Undo',
            run: () => {
                undone = true;
                el.classList.remove('is-removing');
                parent.insertBefore(el, next && next.parentElement === parent ? next : null);
                onChange();
                toast({ type: 'info', icon: 'reset', title: 'Brought back', message: 'The item is back where it was.', key: 'undo' });
            },
        },
    });
};
const unsavedNote = 'Save the section to update the website.';

document.addEventListener('click', (e) => {
    const btn = e.target.closest('button');
    if (!btn) return;

    // Repeater: add item from template (placeholder → unique key).
    if (btn.matches('[data-repeater-add]')) {
        const repeater = btn.closest('[data-repeater]');
        const template = repeater.querySelector(':scope > template[data-repeater-template]');
        const html = template.innerHTML.split(repeater.dataset.placeholder).join(uid());
        const wrap = document.createElement('div');
        wrap.innerHTML = html.trim();
        const item = wrap.firstElementChild;
        item.classList.add('is-new');
        repeater.querySelector(':scope > [data-repeater-items]').appendChild(item);
        enhance(item);
        refreshRepeater(repeater);
        item.querySelector('input:not([type=hidden]), textarea, select')?.focus();
        markDirty(btn.closest('form'));
        const noun = btn.textContent.trim().replace(/^Add\s+/i, '');
        toast({ type: 'success', icon: 'plus', title: `New ${noun} added`, message: `Fill it in, then save the section to publish it.`, key: 'add' });
        return;
    }

    const item = btn.closest('[data-repeater-item]');
    if (item && btn.closest('.ad-repeater__head') === item.querySelector(':scope > .ad-repeater__head')) {
        const repeater = item.closest('[data-repeater]');
        const form = btn.closest('form');
        const title = item.querySelector('[data-item-title]')?.textContent.trim() || 'this item';
        if (btn.matches('[data-item-toggle]')) {
            const collapsed = item.classList.toggle('is-collapsed');
            btn.setAttribute('aria-expanded', String(!collapsed));
            if (!collapsed) enhance(item);
            return;
        }
        if (btn.matches('[data-item-remove]')) {
            confirmDialog({
                title: `Remove “${title}”?`,
                message: 'It disappears from the website when you save this section. You can undo this until then.',
                confirm: 'Remove',
            }).then((ok) => {
                if (!ok) return;
                markDirty(form);
                removeWithUndo(item, {
                    title: `“${title}” removed`,
                    message: unsavedNote,
                    onChange: () => {
                        refreshRepeater(repeater);
                        markDirty(form);
                    },
                });
            });
            return;
        }
        const up = btn.matches('[data-item-up]') && item.previousElementSibling;
        const down = btn.matches('[data-item-down]') && item.nextElementSibling;
        if (up) item.parentElement.insertBefore(item, item.previousElementSibling);
        if (down) item.parentElement.insertBefore(item.nextElementSibling, item);
        if (up || down) {
            btn.focus();
            toast({ type: 'info', icon: 'layers', title: `“${title}” moved ${up ? 'up' : 'down'}`, message: unsavedNote, key: 'move' });
        }
        refreshRepeater(repeater);
        markDirty(form);
        return;
    }

    // Lists of text / numbers.
    if (btn.matches('[data-list-add]')) {
        const list = btn.closest('[data-list]');
        const template = list.querySelector(':scope > template[data-list-template]');
        const wrap = document.createElement('div');
        wrap.innerHTML = template.innerHTML.trim();
        const row = wrap.firstElementChild;
        row.classList.add('is-new');
        list.querySelector(':scope > [data-list-items]').appendChild(row);
        row.querySelector('input, textarea')?.focus();
        markDirty(btn.closest('form'));
        toast({ type: 'success', icon: 'plus', title: btn.textContent.trim().replace(/^Add/i, 'New') + ' added', message: unsavedNote, key: 'add' });
        return;
    }
    const row = btn.closest('[data-list-item]');
    if (row) {
        const form = btn.closest('form');
        const value = row.querySelector('input, textarea')?.value.trim();
        if (btn.matches('[data-list-up]') && row.previousElementSibling) row.parentElement.insertBefore(row, row.previousElementSibling);
        if (btn.matches('[data-list-down]') && row.nextElementSibling) row.parentElement.insertBefore(row.nextElementSibling, row);
        if (btn.matches('[data-list-remove]')) {
            removeWithUndo(row, {
                title: value ? `“${value.length > 40 ? `${value.slice(0, 40)}…` : value}” removed` : 'Empty row removed',
                message: unsavedNote,
                onChange: () => markDirty(form),
            });
        }
        markDirty(form);
        return;
    }

    // Media: remove the current file.
    if (btn.matches('[data-media-remove]')) {
        const media = btn.closest('[data-media]');
        const isVideo = media.dataset.mediaType === 'video';
        confirmDialog({
            title: `Remove this ${isVideo ? 'video' : 'image'}?`,
            message: 'It is taken off the website when you save this section.',
            confirm: 'Remove',
            icon: 'image',
        }).then((ok) => {
            if (!ok) return;
            media.querySelector('[data-media-value]').value = '';
            media.querySelector('[data-media-input]').value = '';
            media.querySelector('[data-media-preview]').querySelectorAll('img, video').forEach((el) => el.remove());
            media.querySelector('.ad-media').classList.add('is-empty');
            media.querySelector('[data-media-path]').textContent = 'Will be removed when you save';
            btn.hidden = true;
            markDirty(btn.closest('form'));
            toast({ type: 'deleted', icon: 'image', title: `${isVideo ? 'Video' : 'Image'} removed`, message: unsavedNote });
        });
    }
});

/* ---------- Media pickers: instant preview + size check ---------- */
const maxUpload = window.adminMaxUpload || 0;
const formatBytes = (b) => (b >= 1048576 ? `${(b / 1048576).toFixed(1)} MB` : `${Math.round(b / 1024)} KB`);
document.addEventListener('change', (e) => {
    const input = e.target;
    if (input.matches('[data-avatar-input]')) {
        const file = input.files[0];
        const preview = $('[data-avatar-preview]');
        if (file && preview) {
            preview.innerHTML = `<img src="${URL.createObjectURL(file)}" alt="">`;
            toast({ type: 'info', icon: 'upload', title: 'New photo selected', message: 'Click “Save profile” to use it.', key: 'media' });
        }
        return;
    }
    if (!input.matches('[data-media-input]')) return;
    const media = input.closest('[data-media]');
    const file = input.files[0];
    if (!file) return;
    if (maxUpload && file.size > maxUpload) {
        toast({ type: 'error', title: 'File too large', message: `That file is ${formatBytes(file.size)}, but the server accepts up to ${formatBytes(maxUpload)}. Please choose a smaller file.` });
        input.value = '';
        return;
    }
    const preview = media.querySelector('[data-media-preview]');
    preview.querySelectorAll('img, video').forEach((el) => el.remove());
    const isVideo = media.dataset.mediaType === 'video';
    const el = document.createElement(isVideo ? 'video' : 'img');
    el.src = URL.createObjectURL(file);
    if (isVideo) el.muted = true;
    preview.prepend(el);
    media.querySelector('.ad-media').classList.remove('is-empty');
    media.querySelector('[data-media-path]').textContent = `${file.name} · ${formatBytes(file.size)} — uploads when you save`;
    media.querySelector('[data-media-remove]')?.removeAttribute('hidden');
    toast({ type: 'info', icon: 'upload', title: `${isVideo ? 'Video' : 'Image'} ready`, message: 'It uploads when you save the section.', key: 'media' });
});

/* ---------- Unsaved changes ---------- */
document.addEventListener('submit', (e) => {
    if (e.defaultPrevented) return;
    const form = e.target;
    delete form.dataset.dirty;
    const save = form.querySelector('[data-save]');
    if (save) {
        save.disabled = true;
        save.querySelector('span').textContent = 'Saving…';
    }
    window.__adminSubmitting = true;
});
window.addEventListener('beforeunload', (e) => {
    if (!window.__adminSubmitting && $$('form[data-dirty]').length) {
        e.preventDefault();
        e.returnValue = '';
    }
});

enhance(document);
fromHash();
