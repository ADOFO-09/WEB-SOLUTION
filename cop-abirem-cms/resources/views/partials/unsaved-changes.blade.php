{{--
    Unsaved Changes Guard
    Auto-detects all PUT/PATCH edit forms plus any form with data-track-changes="true".
    Shows a custom modal instead of the browser confirm dialog when the user tries to
    navigate away with unsaved edits.
--}}

<div id="unsaved-changes-overlay"
     style="display:none;position:fixed;inset:0;z-index:9999;
            background:rgba(0,0,0,0.55);backdrop-filter:blur(3px);"
     aria-modal="true" role="alertdialog"
     aria-labelledby="unsaved-modal-title"
     aria-describedby="unsaved-modal-desc">

    <div style="position:absolute;top:50%;left:50%;transform:translate(-50%,-50%);
                background:#ffffff;border-radius:1rem;padding:2rem;
                max-width:22rem;width:90%;
                box-shadow:0 25px 60px rgba(0,0,0,0.3);">

        {{-- Warning icon --}}
        <div style="text-align:center;margin-bottom:1.25rem;">
            <div style="width:3.5rem;height:3.5rem;border-radius:50%;background:#fef3c7;
                        margin:0 auto 1rem;
                        display:flex;align-items:center;justify-content:center;">
                <svg style="width:1.75rem;height:1.75rem;color:#d97706;"
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0
                             2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464
                             0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            <h3 id="unsaved-modal-title"
                style="font-size:1.125rem;font-weight:700;color:#111827;margin:0 0 0.5rem;">
                Unsaved Changes
            </h3>
            <p id="unsaved-modal-desc"
               style="font-size:0.875rem;color:#6b7280;line-height:1.6;margin:0;">
                You have unsaved changes on this page.<br>
                If you leave now, your changes will be lost.
            </p>
        </div>

        {{-- Buttons: primary = Stay, secondary = Leave --}}
        <div style="display:flex;flex-direction:column;gap:0.625rem;margin-top:1.5rem;">
            <button id="unsaved-stay-btn"
                    type="button"
                    style="width:100%;padding:0.675rem 1rem;border:none;border-radius:0.5rem;
                           background:#1e3a5f;color:#ffffff;font-size:0.875rem;font-weight:600;
                           cursor:pointer;transition:background 0.15s;line-height:1.5;"
                    onmouseover="this.style.background='#16304f'"
                    onmouseout="this.style.background='#1e3a5f'">
                Stay on page
            </button>
            <button id="unsaved-leave-btn"
                    type="button"
                    style="width:100%;padding:0.675rem 1rem;border:1px solid #d1d5db;border-radius:0.5rem;
                           background:#ffffff;color:#374151;font-size:0.875rem;font-weight:500;
                           cursor:pointer;transition:background 0.15s;line-height:1.5;"
                    onmouseover="this.style.background='#f9fafb'"
                    onmouseout="this.style.background='#ffffff'">
                Leave without saving
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    'use strict';

    var overlay  = document.getElementById('unsaved-changes-overlay');
    var stayBtn  = document.getElementById('unsaved-stay-btn');
    var leaveBtn = document.getElementById('unsaved-leave-btn');

    var isDirty   = false;
    var pendingNav = null;

    /* ─── Helpers ────────────────────────────────────────── */
    function markDirty()  { isDirty = true;  }
    function markClean()  { isDirty = false; }

    function showModal(href) {
        pendingNav = href;
        overlay.style.display = 'block';
        stayBtn.focus();
    }

    function hideModal() {
        overlay.style.display = 'none';
        pendingNav = null;
    }

    /* ─── Form detection ────────────────────────────────── */
    function getTrackedForms() {
        var tracked = [];
        document.querySelectorAll('form').forEach(function (form) {
            /* Auto-detect: any edit form (PUT / PATCH method override) */
            var m = form.querySelector('input[name="_method"]');
            if (m && (m.value === 'PUT' || m.value === 'PATCH')) {
                tracked.push(form);
                return;
            }
            /* Explicit opt-in: settings pages, profile pages, etc. */
            if (form.getAttribute('data-track-changes') === 'true') {
                tracked.push(form);
            }
        });
        return tracked;
    }

    /* ─── Boot ──────────────────────────────────────────── */
    function init() {
        var forms = getTrackedForms();
        if (forms.length === 0) return;   /* nothing to guard on this page */

        /* Watch all tracked forms for changes */
        forms.forEach(function (form) {
            form.addEventListener('input',  markDirty);
            form.addEventListener('change', markDirty);
        });

        /* Any form submission (save, delete, void…) clears dirty state
           so we never block a legitimate in-page action. */
        document.addEventListener('submit', markClean);

        /* ─ Intercept in-app link clicks ─ */
        document.addEventListener('click', function (e) {
            if (!isDirty) return;

            var link = e.target.closest('a[href]');
            if (!link) return;

            var href = link.getAttribute('href');
            if (!href
                || href === '#'
                || href.startsWith('javascript:')
                || link.getAttribute('target') === '_blank') return;

            e.preventDefault();
            e.stopImmediatePropagation();
            showModal(href);
        }, true /* capture — fires before Alpine handlers */);

        /* ─ Fallback: browser back / refresh / close tab ─ */
        window.addEventListener('beforeunload', function (e) {
            if (isDirty) {
                e.preventDefault();
                e.returnValue = '';   /* required by Chrome / Firefox */
            }
        });
    }

    /* ─── Modal button handlers ─────────────────────────── */
    stayBtn.addEventListener('click', hideModal);

    leaveBtn.addEventListener('click', function () {
        markClean();
        hideModal();
        if (pendingNav) window.location.href = pendingNav;
    });

    /* Close on backdrop click */
    overlay.addEventListener('click', function (e) {
        if (e.target === overlay) hideModal();
    });

    /* Close on Escape key */
    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape' && overlay.style.display !== 'none') hideModal();
    });

    /* Wait for DOM if the script runs in <head> */
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
}());
</script>
