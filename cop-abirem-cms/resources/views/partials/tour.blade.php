@auth
{{-- ═══════════════════════════════════════════════���═══════
     Guided Onboarding Tour  —  Driver.js v1
     Loaded on every authenticated page; auto-starts only
     on the user's first login (has_completed_tour = false).
     The global window.startGuidedTour() lets any "Take a
     Tour" button replay it at any time.
═══════════════════════════════════════════════════════ --}}
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1/dist/driver.css"/>
<script src="https://cdn.jsdelivr.net/npm/driver.js@1/dist/driver.js.iife.js"></script>

<style>
/* ── Custom Driver.js overrides to match the app's colour palette ── */
.driver-popover {
    font-family: 'Plus Jakarta Sans', sans-serif;
    border-radius: 12px;
    box-shadow: 0 20px 60px rgba(0,0,0,.25);
    max-width: 320px;
}
.driver-popover-title {
    font-size: .95rem;
    font-weight: 700;
    color: #1e3a5f;
    margin-bottom: .3rem;
}
.driver-popover-description {
    font-size: .84rem;
    color: #475569;
    line-height: 1.55;
}
.driver-popover-footer button {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: .8rem;
    font-weight: 600;
    border-radius: 7px;
    padding: .4rem .85rem;
}
.driver-popover-next-btn {
    background: #1e3a5f !important;
    border-color: #1e3a5f !important;
    color: #fff !important;
}
.driver-popover-next-btn:hover {
    background: #2d5a8a !important;
    border-color: #2d5a8a !important;
}
.driver-popover-prev-btn,
.driver-popover-close-btn {
    color: #475569 !important;
}
.driver-popover-progress-text {
    font-size: .75rem;
    color: #94a3b8;
}
</style>

<script>
(function () {
    'use strict';

    /* ── Config injected from server ── */
    var TOUR = {
        autoStart:   {{ auth()->user()->has_completed_tour ? 'false' : 'true' }},
        completeUrl: '{{ route("tour.complete") }}',
        csrfToken:   '{{ csrf_token() }}',
        role:        '{{ addslashes(auth()->user()->role->name ?? "Member") }}'
    };

    /* ── Role-based step definitions ─────────────────────────────────
       Each step's `element` is a CSS attribute selector that targets
       a data-tour="..." attribute added to the relevant DOM element.
       Steps whose element is absent or not visible are skipped.
    ─────────────────────────────────────────────────────────────────── */
    var STEPS = {

        'System Administrator': [
            {
                element: '[data-tour="nav-dashboard"]',
                popover: { title: 'Your Dashboard', side: 'right', align: 'start',
                    description: 'This is your dashboard — a quick overview of the church at a glance. Key statistics are shown here.' }
            },
            {
                element: '[data-tour="nav-people"]',
                popover: { title: 'Church Members & People', side: 'right', align: 'start',
                    description: 'Manage all your church members, ministries, and visitors here.' }
            },
            {
                element: '[data-tour="nav-attendance"]',
                popover: { title: 'Attendance', side: 'right', align: 'start',
                    description: 'Record attendance for services using the name list, QR code, or fingerprint scanner.' }
            },
            {
                element: '[data-tour="nav-finance"]',
                popover: { title: 'Finance', side: 'right', align: 'start',
                    description: 'Record tithes, offerings, and expenses, and manage all church finances here.' }
            },
            {
                element: '[data-tour="nav-communication"]',
                popover: { title: 'Communication', side: 'right', align: 'start',
                    description: 'Send SMS messages to members individually or in groups from here.' }
            },
            {
                element: '[data-tour="nav-reports"]',
                popover: { title: 'Reports', side: 'right', align: 'start',
                    description: 'Generate financial statements, membership lists, attendance summaries, and more.' }
            },
            {
                element: '[data-tour="nav-users"]',
                popover: { title: 'User Management', side: 'right', align: 'start',
                    description: 'Create and manage staff accounts and their access levels here.' }
            },
            {
                element: '[data-tour="nav-settings"]',
                popover: { title: 'Settings', side: 'right', align: 'start',
                    description: 'Set up your church name, logo, and configure system preferences here.' }
            },
            {
                element: '[data-tour="topnav-account"]',
                popover: { title: 'Your Account', side: 'bottom', align: 'end',
                    description: 'Click here to update your profile, change your password, or sign out.' }
            }
        ],

        'Presiding Elder': [
            {
                element: '[data-tour="nav-dashboard"]',
                popover: { title: 'Your Dashboard', side: 'right', align: 'start',
                    description: 'Welcome! This is your dashboard — an overview of key church information at a glance.' }
            },
            {
                element: '[data-tour="nav-people"]',
                popover: { title: 'Church Members', side: 'right', align: 'start',
                    description: 'View member profiles and ministry details from here.' }
            },
            {
                element: '[data-tour="nav-finance"]',
                popover: { title: 'Finance', side: 'right', align: 'start',
                    description: 'Review income, expenses, and fund summaries. Expense approvals are also managed here.' }
            },
            {
                element: '[data-tour="nav-reports"]',
                popover: { title: 'Reports', side: 'right', align: 'start',
                    description: 'Access financial statements, attendance records, and membership reports from here.' }
            },
            {
                element: '[data-tour="topnav-account"]',
                popover: { title: 'Your Account', side: 'bottom', align: 'end',
                    description: 'Click here to manage your profile and settings.' }
            }
        ],

        'Local Secretary': [
            {
                element: '[data-tour="nav-dashboard"]',
                popover: { title: 'Your Dashboard', side: 'right', align: 'start',
                    description: 'Welcome! Your secretary dashboard gives you a quick church overview.' }
            },
            {
                element: '[data-tour="nav-people"]',
                popover: { title: 'Members', side: 'right', align: 'start',
                    description: 'View and manage the church membership register from here.' }
            },
            {
                element: '[data-tour="nav-attendance"]',
                popover: { title: 'Attendance', side: 'right', align: 'start',
                    description: 'Record and review attendance for all church services and programmes.' }
            },
            {
                element: '[data-tour="nav-reports"]',
                popover: { title: 'Reports', side: 'right', align: 'start',
                    description: 'Generate membership and attendance reports from here.' }
            },
            {
                element: '[data-tour="topnav-account"]',
                popover: { title: 'Your Account', side: 'bottom', align: 'end',
                    description: 'Update your profile and settings here.' }
            }
        ],

        'Financial Secretary': [
            {
                element: '[data-tour="nav-dashboard"]',
                popover: { title: 'Your Dashboard', side: 'right', align: 'start',
                    description: 'Welcome! Your financial dashboard shows a summary of the church\'s income and expenses.' }
            },
            {
                element: '[data-tour="nav-finance"]',
                popover: { title: 'Finance', side: 'right', align: 'start',
                    description: 'Record tithes, offerings, donations, and expenses — and manage pledges all in one place.' }
            },
            {
                element: '[data-tour="nav-reports"]',
                popover: { title: 'Reports', side: 'right', align: 'start',
                    description: 'Generate income statements, ledgers, and financial summaries from here.' }
            },
            {
                element: '[data-tour="topnav-account"]',
                popover: { title: 'Your Account', side: 'bottom', align: 'end',
                    description: 'Update your profile and settings here.' }
            }
        ],

        'Ministry Leader': [
            {
                element: '[data-tour="nav-dashboard"]',
                popover: { title: 'Your Dashboard', side: 'right', align: 'start',
                    description: 'Welcome! Your ministry dashboard shows your ministry\'s latest activity.' }
            },
            {
                element: '[data-tour="nav-people"]',
                popover: { title: 'Your Ministry', side: 'right', align: 'start',
                    description: 'View your ministry group, manage members, and check assignments here.' }
            },
            {
                element: '[data-tour="nav-attendance"]',
                popover: { title: 'Attendance', side: 'right', align: 'start',
                    description: 'Record and view attendance for your ministry\'s meetings and programmes.' }
            },
            {
                element: '[data-tour="topnav-account"]',
                popover: { title: 'Your Account', side: 'bottom', align: 'end',
                    description: 'Manage your profile and settings here.' }
            }
        ],

        'Member': [
            {
                element: '[data-tour="member-nav-dashboard"]',
                popover: { title: 'Welcome to Your Portal', side: 'right', align: 'start',
                    description: 'This is your personal church portal. Everything about your membership is here.' }
            },
            {
                element: '[data-tour="member-nav-profile"]',
                popover: { title: 'My Profile', side: 'right', align: 'start',
                    description: 'View and update your personal details, contact information, and photo here.' }
            },
            {
                element: '[data-tour="member-nav-attendance"]',
                popover: { title: 'My Attendance', side: 'right', align: 'start',
                    description: 'See your attendance history for all church services, and scan a QR code to check in.' }
            },
            {
                element: '[data-tour="member-nav-giving"]',
                popover: { title: 'Giving & Pledges', side: 'right', align: 'start',
                    description: 'View your tithes, offerings, donations, and pledges — and download your giving statement.' }
            },
            {
                element: '[data-tour="member-topnav-tour"]',
                popover: { title: 'Replay This Tour', side: 'bottom', align: 'end',
                    description: 'You can restart this tour anytime by clicking the "Take a Tour" button up here.' }
            }
        ]
    };

    /* ── Mark tour complete on the server (fire-and-forget) ── */
    function markComplete() {
        try {
            fetch(TOUR.completeUrl, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': TOUR.csrfToken,
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            });
        } catch (e) { /* fail silently */ }
    }

    /* ── Build and launch the tour ── */
    function startTour() {
        try {
            if (!window.driver || !window.driver.js || !window.driver.js.driver) return;

            var rawSteps = STEPS[TOUR.role] || [];

            /* Skip steps whose target is absent or not visible in the viewport */
            var steps = rawSteps.filter(function (step) {
                if (!step.element) return true;
                var el = document.querySelector(step.element);
                if (!el) return false;
                var r = el.getBoundingClientRect();
                return r.width > 0 && r.height > 0;
            });

            if (!steps.length) return;

            var driverObj = window.driver.js.driver({
                showProgress:   true,
                progressText:   '@{{current}} of @{{total}}',
                allowClose:     true,
                overlayOpacity: 0.6,
                smoothScroll:   true,
                nextBtnText:    'Next →',
                prevBtnText:    '← Back',
                doneBtnText:    'Finish ✓',
                steps:          steps,
                onDestroyStarted: function () {
                    markComplete();
                    driverObj.destroy();
                }
            });

            driverObj.drive();
        } catch (e) { /* fail silently — never block the app */ }
    }

    /* ── Expose globally for "Take a Tour" buttons ── */
    window.startGuidedTour = startTour;

    /* ── Auto-start on first login, after page has fully rendered ── */
    if (TOUR.autoStart) {
        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', function () {
                setTimeout(startTour, 700);
            });
        } else {
            setTimeout(startTour, 700);
        }
    }
}());
</script>
@endauth
