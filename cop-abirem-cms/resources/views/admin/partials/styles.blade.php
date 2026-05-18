<style>
    /* ================================================================
       CSS Custom Properties
    ================================================================ */
    :root {
        --primary:        #1e3a5f;
        --primary-light:  #2d5a8a;
        --primary-dark:   #0f2744;
        --accent:         #d4af37;
        --accent-light:   #e8c94b;
        --success:        #10b981;
        --warning:        #f59e0b;
        --danger:         #ef4444;
        --info:           #3b82f6;
        --sidebar-width:  260px;

        /* Shadows */
        --shadow-xs: 0 1px 2px rgba(0,0,0,0.05);
        --shadow-sm: 0 1px 3px rgba(0,0,0,0.07), 0 1px 2px rgba(0,0,0,0.04);
        --shadow-md: 0 4px 6px -1px rgba(0,0,0,0.07), 0 2px 4px -1px rgba(0,0,0,0.04);
        --shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -2px rgba(0,0,0,0.04);
        --shadow-xl: 0 20px 25px -5px rgba(0,0,0,0.08), 0 10px 10px -5px rgba(0,0,0,0.03);

        /* Border radius */
        --radius-sm: 0.375rem;
        --radius-md: 0.5rem;
        --radius-lg: 0.75rem;
        --radius-xl: 1rem;

        /* Transition */
        --transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
    }

    /* ================================================================
       Base
    ================================================================ */
    * {
        font-family: 'Plus Jakarta Sans', sans-serif;
        box-sizing: border-box;
    }

    html {
        scroll-behavior: smooth;
    }

    body {
        background: #f0f4f8;
        background-image: radial-gradient(circle at 20% 50%, rgba(30,58,95,0.03) 0%, transparent 50%),
                          radial-gradient(circle at 80% 20%, rgba(212,175,55,0.03) 0%, transparent 50%);
        color: #1e293b;
        min-height: 100vh;
    }

    /* ================================================================
       Sidebar
    ================================================================ */
    .sidebar {
        width: var(--sidebar-width);
        background: linear-gradient(165deg, #0f2744 0%, #1a3558 45%, #1e3a5f 100%);
        height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 40;
        transition: transform 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        box-shadow: 4px 0 24px rgba(0,0,0,0.12);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .sidebar::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(255,255,255,0.03) 0%, transparent 100%);
        pointer-events: none;
    }

    .sidebar-logo {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.08);
        background: rgba(0,0,0,0.15);
    }

    .sidebar-logo h1 {
        color: var(--accent);
        font-weight: 700;
        font-size: 1.125rem;
        letter-spacing: -0.02em;
    }

    .sidebar-logo span {
        color: rgba(255,255,255,0.55);
        font-size: 0.7rem;
        letter-spacing: 0.05em;
        text-transform: uppercase;
    }

    .sidebar-nav {
        padding: 0.75rem 0;
        overflow-y: scroll;
        flex: 1;
        min-height: 0;
        /* Hide scrollbar by default */
        scrollbar-width: none;
    }

    .sidebar-nav::-webkit-scrollbar {
        width: 0;
        background: transparent;
    }

    /* Show thin scrollbar on hover */
    .sidebar-nav:hover {
        scrollbar-width: thin;
        scrollbar-color: rgba(255,255,255,0.18) transparent;
    }

    .sidebar-nav:hover::-webkit-scrollbar {
        width: 4px;
    }

    .sidebar-nav:hover::-webkit-scrollbar-track {
        background: transparent;
    }

    .sidebar-nav:hover::-webkit-scrollbar-thumb {
        background: rgba(255,255,255,0.2);
        border-radius: 2px;
    }

    .nav-section {
        padding: 0 0.75rem;
        margin-top: 0.25rem;
    }

    .nav-section-title {
        color: rgba(255,255,255,0.35);
        font-size: 0.625rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.12em;
        padding: 0.75rem 0.75rem 0.375rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.625rem 0.875rem;
        margin-bottom: 2px;
        border-radius: var(--radius-md);
        color: rgba(255,255,255,0.65);
        text-decoration: none;
        font-size: 0.8125rem;
        font-weight: 500;
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .nav-link::before {
        content: '';
        position: absolute;
        left: 0;
        top: 50%;
        transform: translateY(-50%);
        width: 3px;
        height: 0;
        background: var(--accent);
        border-radius: 0 2px 2px 0;
        transition: height 0.2s ease;
    }

    .nav-link:hover {
        background: rgba(255,255,255,0.08);
        color: #fff;
    }

    .nav-link:hover::before {
        height: 60%;
    }

    .nav-link.active {
        background: rgba(212,175,55,0.15);
        color: var(--accent-light);
        font-weight: 600;
    }

    .nav-link.active::before {
        height: 70%;
        background: var(--accent);
    }

    .nav-link svg {
        width: 1.125rem;
        height: 1.125rem;
        margin-right: 0.75rem;
        flex-shrink: 0;
        opacity: 0.85;
        transition: opacity 0.2s;
    }

    .nav-link:hover svg,
    .nav-link.active svg {
        opacity: 1;
    }

    /* ================================================================
       Main Content
    ================================================================ */
    .main-content {
        margin-left: var(--sidebar-width);
        min-height: 100vh;
        display: flex;
        flex-direction: column;
    }

    /* ================================================================
       Top Navigation
    ================================================================ */
    .top-nav {
        background: rgba(255,255,255,0.95);
        backdrop-filter: blur(8px);
        -webkit-backdrop-filter: blur(8px);
        border-bottom: 1px solid rgba(226,232,240,0.8);
        padding: 0 1.5rem;
        height: 60px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 30;
        box-shadow: 0 1px 0 rgba(0,0,0,0.04), 0 2px 8px rgba(0,0,0,0.04);
    }

    /* ================================================================
       Page Header
    ================================================================ */
    .page-header {
        padding: 1.25rem 1.75rem;
        background: #fff;
        border-bottom: 1px solid #e8edf3;
        position: relative;
    }

    .page-header::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 60px;
        height: 2px;
        background: linear-gradient(90deg, var(--primary), var(--primary-light));
        border-radius: 0 2px 2px 0;
    }

    .page-header h1 {
        font-size: 1.375rem;
        font-weight: 700;
        color: var(--primary-dark);
        letter-spacing: -0.02em;
    }

    /* ================================================================
       Content Area
    ================================================================ */
    .content-area {
        padding: 1.5rem 1.75rem;
        flex: 1;
    }

    /* ================================================================
       Cards
    ================================================================ */
    .card {
        background: #fff;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-sm);
        border: 1px solid rgba(226,232,240,0.8);
        transition: box-shadow 0.2s ease;
    }

    .card:hover {
        box-shadow: var(--shadow-md);
    }

    .card-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-body {
        padding: 1.25rem;
    }

    /* ================================================================
       Buttons
    ================================================================ */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        gap: 0.375rem;
        padding: 0.5rem 1.125rem;
        font-size: 0.8125rem;
        font-weight: 600;
        border-radius: var(--radius-md);
        transition: var(--transition);
        cursor: pointer;
        text-decoration: none;
        border: none;
        line-height: 1.5;
        white-space: nowrap;
    }

    .btn:active {
        transform: scale(0.98);
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
        box-shadow: 0 1px 3px rgba(30,58,95,0.3), 0 1px 2px rgba(30,58,95,0.2);
    }

    .btn-primary:hover {
        background: var(--primary-light);
        box-shadow: 0 4px 8px rgba(30,58,95,0.25);
        transform: translateY(-1px);
    }

    .btn-secondary {
        background: #f8fafc;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: #334155;
    }

    .btn-success {
        background: var(--success);
        color: #fff;
        box-shadow: 0 1px 3px rgba(16,185,129,0.3);
    }

    .btn-success:hover {
        background: #059669;
        box-shadow: 0 4px 8px rgba(16,185,129,0.25);
        transform: translateY(-1px);
    }

    .btn-danger {
        background: var(--danger);
        color: #fff;
        box-shadow: 0 1px 3px rgba(239,68,68,0.3);
    }

    .btn-danger:hover {
        background: #dc2626;
        box-shadow: 0 4px 8px rgba(239,68,68,0.25);
        transform: translateY(-1px);
    }

    .btn-warning {
        background: var(--warning);
        color: #fff;
        box-shadow: 0 1px 3px rgba(245,158,11,0.3);
    }

    .btn-warning:hover {
        background: #d97706;
        transform: translateY(-1px);
    }

    .btn-sm {
        padding: 0.3125rem 0.75rem;
        font-size: 0.75rem;
        border-radius: var(--radius-sm);
    }

    .btn-icon {
        padding: 0.5rem;
        border-radius: var(--radius-sm);
    }

    /* ================================================================
       Form Controls
    ================================================================ */
    .form-group {
        margin-bottom: 1.125rem;
    }

    .form-label {
        display: block;
        font-size: 0.8125rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.4rem;
        letter-spacing: -0.01em;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 0.5625rem 0.875rem;
        font-size: 0.875rem;
        border: 1.5px solid #e2e8f0;
        border-radius: var(--radius-md);
        transition: var(--transition);
        background: #fff;
        color: #1e293b;
    }

    .form-input:hover, .form-select:hover, .form-textarea:hover {
        border-color: #cbd5e1;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(30,58,95,0.08);
        background: #fafcff;
    }

    .form-input::placeholder {
        color: #94a3b8;
    }

    .form-error {
        color: var(--danger);
        font-size: 0.75rem;
        margin-top: 0.3rem;
        display: flex;
        align-items: center;
        gap: 0.25rem;
    }

    .form-hint {
        color: #94a3b8;
        font-size: 0.75rem;
        margin-top: 0.3rem;
    }

    /* ================================================================
       Tables
    ================================================================ */
    .table-container {
        overflow-x: auto;
        -webkit-overflow-scrolling: touch;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        text-align: left;
        padding: 0.75rem 1rem;
        font-size: 0.6875rem;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 0.07em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 2px solid #e8edf3;
        white-space: nowrap;
    }

    .table td {
        padding: 0.875rem 1rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.875rem;
        color: #334155;
    }

    .table tbody tr:last-child td {
        border-bottom: none;
    }

    .table tbody tr {
        transition: background 0.15s ease;
    }

    .table tbody tr:hover {
        background: #f8fafc;
    }

    /* ================================================================
       Badges
    ================================================================ */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.2rem 0.6rem;
        font-size: 0.6875rem;
        font-weight: 700;
        border-radius: 9999px;
        letter-spacing: 0.02em;
        white-space: nowrap;
    }

    .badge-success {
        background: #d1fae5;
        color: #065f46;
    }

    .badge-warning {
        background: #fef3c7;
        color: #92400e;
    }

    .badge-danger {
        background: #fee2e2;
        color: #991b1b;
    }

    .badge-info {
        background: #dbeafe;
        color: #1e40af;
    }

    .badge-secondary {
        background: #f1f5f9;
        color: #475569;
    }

    /* ================================================================
       Alerts
    ================================================================ */
    .alert {
        padding: 0.875rem 1.125rem;
        border-radius: var(--radius-md);
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
        gap: 0.75rem;
        font-size: 0.875rem;
    }

    .alert-success {
        background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
        color: #065f46;
        border: 1px solid #a7f3d0;
        border-left: 4px solid var(--success);
    }

    .alert-warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fffbeb 100%);
        color: #92400e;
        border: 1px solid #fde68a;
        border-left: 4px solid var(--warning);
    }

    .alert-danger {
        background: linear-gradient(135deg, #fee2e2 0%, #fff5f5 100%);
        color: #991b1b;
        border: 1px solid #fecaca;
        border-left: 4px solid var(--danger);
    }

    .alert-info {
        background: linear-gradient(135deg, #dbeafe 0%, #eff6ff 100%);
        color: #1e40af;
        border: 1px solid #bfdbfe;
        border-left: 4px solid var(--info);
    }

    /* ================================================================
       Stat Cards
    ================================================================ */
    .stat-card {
        background: #fff;
        border-radius: var(--radius-lg);
        padding: 1.25rem 1.5rem;
        border: 1px solid rgba(226,232,240,0.8);
        box-shadow: var(--shadow-sm);
        transition: var(--transition);
        position: relative;
        overflow: hidden;
    }

    .stat-card::after {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 60px;
        height: 60px;
        border-radius: 0 var(--radius-lg) 0 60px;
        background: linear-gradient(135deg, rgba(30,58,95,0.04), transparent);
        pointer-events: none;
    }

    .stat-card:hover {
        box-shadow: var(--shadow-md);
        transform: translateY(-1px);
    }

    .stat-card-icon {
        width: 2.75rem;
        height: 2.75rem;
        border-radius: var(--radius-md);
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 0.875rem;
    }

    .stat-card-value {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--primary-dark);
        letter-spacing: -0.03em;
        line-height: 1.1;
    }

    .stat-card-label {
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.25rem;
        font-weight: 500;
        letter-spacing: 0.01em;
    }

    /* ================================================================
       Dropdown
    ================================================================ */
    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        top: calc(100% + 0.5rem);
        background: #fff;
        border: 1px solid #e8edf3;
        border-radius: var(--radius-lg);
        box-shadow: var(--shadow-xl);
        min-width: 13rem;
        z-index: 50;
        display: none;
        overflow: hidden;
    }

    .dropdown-menu.show {
        display: block;
        animation: dropdownFadeIn 0.15s ease;
    }

    @keyframes dropdownFadeIn {
        from { opacity: 0; transform: translateY(-6px); }
        to   { opacity: 1; transform: translateY(0); }
    }

    .dropdown-item {
        display: block;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        color: #374151;
        text-decoration: none;
        transition: background 0.15s;
    }

    .dropdown-item:hover {
        background: #f8fafc;
        color: var(--primary);
    }

    .dropdown-divider {
        border-top: 1px solid #f1f5f9;
        margin: 0.375rem 0;
    }

    /* ================================================================
       Modals
    ================================================================ */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(15,27,44,0.55);
        backdrop-filter: blur(2px);
        -webkit-backdrop-filter: blur(2px);
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }

    .modal {
        background: #fff;
        border-radius: var(--radius-xl);
        max-width: 32rem;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        box-shadow: var(--shadow-xl);
        animation: modalSlideIn 0.2s cubic-bezier(0.34, 1.56, 0.64, 1);
    }

    @keyframes modalSlideIn {
        from { opacity: 0; transform: scale(0.96) translateY(8px); }
        to   { opacity: 1; transform: scale(1) translateY(0); }
    }

    .modal-header {
        padding: 1.125rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
        background: #f8fafc;
        border-radius: 0 0 var(--radius-xl) var(--radius-xl);
    }

    /* ================================================================
       Mobile Menu Toggle
    ================================================================ */
    .mobile-menu-btn {
        display: none;
    }

    /* ================================================================
       Pagination
    ================================================================ */
    .pagination {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2.125rem;
        height: 2.125rem;
        padding: 0 0.5rem;
        font-size: 0.8125rem;
        border-radius: var(--radius-sm);
        text-decoration: none;
        transition: var(--transition);
        font-weight: 500;
    }

    .pagination a {
        color: #475569;
        background: #fff;
        border: 1px solid #e2e8f0;
    }

    .pagination a:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
        color: var(--primary);
    }

    .pagination .active span {
        background: var(--primary);
        color: #fff;
        border: 1px solid var(--primary);
        box-shadow: 0 2px 4px rgba(30,58,95,0.3);
    }

    .pagination .disabled span {
        color: #cbd5e1;
        background: #f8fafc;
        border: 1px solid #f1f5f9;
        cursor: not-allowed;
    }

    /* ================================================================
       Checkbox Group
    ================================================================ */
    .checkbox-group {
        display: flex;
        flex-wrap: wrap;
        gap: 0.5rem;
    }

    .checkbox-item {
        display: flex;
        align-items: center;
        padding: 0.5rem 0.75rem;
        background: #f8fafc;
        border: 1.5px solid #e2e8f0;
        border-radius: var(--radius-sm);
        cursor: pointer;
        transition: var(--transition);
        font-size: 0.875rem;
    }

    .checkbox-item:hover {
        background: #f1f5f9;
        border-color: #cbd5e1;
    }

    .checkbox-item.checked {
        background: #eff6ff;
        border-color: #93c5fd;
        color: var(--info);
    }

    .checkbox-item input[type="checkbox"] {
        margin-right: 0.5rem;
        accent-color: var(--primary);
    }

    /* ================================================================
       Loading States
    ================================================================ */
    @keyframes shimmer {
        0%   { background-position: -200% center; }
        100% { background-position:  200% center; }
    }

    .skeleton {
        background: linear-gradient(90deg, #f0f4f8 25%, #e8edf3 50%, #f0f4f8 75%);
        background-size: 200% 100%;
        animation: shimmer 1.4s infinite;
        border-radius: var(--radius-sm);
    }

    @keyframes spin {
        to { transform: rotate(360deg); }
    }

    .spinner {
        width: 1.25rem;
        height: 1.25rem;
        border: 2px solid rgba(30,58,95,0.15);
        border-top-color: var(--primary);
        border-radius: 50%;
        animation: spin 0.7s linear infinite;
        display: inline-block;
    }

    /* ================================================================
       Responsive
    ================================================================ */
    @media (max-width: 1024px) {
        .sidebar {
            transform: translateX(-100%);
            z-index: 45;
        }

        .sidebar.open {
            transform: translateX(0);
        }

        .main-content {
            margin-left: 0;
        }

        .mobile-menu-btn {
            display: block;
        }

        .content-area {
            padding: 1rem;
        }

        .page-header {
            padding: 1rem;
        }
    }

    @media (max-width: 768px) {
        .top-nav {
            padding: 0 1rem;
        }

        .content-area {
            padding: 0.875rem;
        }

        .stat-card {
            padding: 1rem 1.125rem;
        }

        .stat-card-value {
            font-size: 1.5rem;
        }

        .modal {
            margin: 0.5rem;
            border-radius: var(--radius-lg);
        }
    }

    /* ================================================================
       Utility Classes
    ================================================================ */
    .text-muted    { color: #64748b; }
    .text-sm       { font-size: 0.875rem; }
    .text-xs       { font-size: 0.75rem; }
    .font-medium   { font-weight: 500; }
    .font-semibold { font-weight: 600; }
    .mt-4  { margin-top: 1rem; }
    .mb-4  { margin-bottom: 1rem; }

    .flex            { display: flex; }
    .items-center    { align-items: center; }
    .justify-between { justify-content: space-between; }
    .gap-2 { gap: 0.5rem; }
    .gap-4 { gap: 1rem; }

    .grid          { display: grid; }
    .grid-cols-2   { grid-template-columns: repeat(2, minmax(0, 1fr)); }
    .grid-cols-3   { grid-template-columns: repeat(3, minmax(0, 1fr)); }
    .grid-cols-4   { grid-template-columns: repeat(4, minmax(0, 1fr)); }

    @media (max-width: 768px) {
        .grid-cols-2, .grid-cols-3, .grid-cols-4 {
            grid-template-columns: 1fr;
        }
    }

    /* ================================================================
       Print Styles
    ================================================================ */
    @media print {
        .sidebar,
        .top-nav,
        .page-header,
        .mobile-menu-btn,
        [x-data],
        nav[aria-label="pagination"] {
            display: none !important;
        }

        .main-content {
            margin-left: 0 !important;
            min-height: 0 !important;
        }

        .content-area {
            padding: 0 !important;
        }

        body {
            background: white !important;
        }

        .card {
            box-shadow: none !important;
            border: 1px solid #e2e8f0 !important;
        }
    }
</style>
