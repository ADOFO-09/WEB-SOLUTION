<style>
    :root {
        --primary: #1e3a5f;
        --primary-light: #2d5a8a;
        --primary-dark: #0f2744;
        --accent: #d4af37;
        --accent-light: #e8c94b;
        --success: #10b981;
        --warning: #f59e0b;
        --danger: #ef4444;
        --sidebar-width: 280px;
    }

    * {
        font-family: 'Plus Jakarta Sans', sans-serif;
    }

    body {
        background-color: #f1f5f9;
    }

    /* Sidebar Styles */
    .sidebar {
        width: var(--sidebar-width);
        background: linear-gradient(180deg, var(--primary-dark) 0%, var(--primary) 100%);
        min-height: 100vh;
        position: fixed;
        left: 0;
        top: 0;
        z-index: 40;
        transition: transform 0.3s ease;
    }

    .sidebar-logo {
        padding: 1.5rem;
        border-bottom: 1px solid rgba(255,255,255,0.1);
    }

    .sidebar-logo h1 {
        color: var(--accent);
        font-weight: 700;
        font-size: 1.25rem;
    }

    .sidebar-logo span {
        color: rgba(255,255,255,0.7);
        font-size: 0.75rem;
    }

    .sidebar-nav {
        padding: 1rem 0;
        overflow-y: auto;
        max-height: calc(100vh - 150px);
    }

    .nav-section {
        padding: 0.5rem 1rem;
        margin-top: 0.5rem;
    }

    .nav-section-title {
        color: rgba(255,255,255,0.4);
        font-size: 0.65rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.1em;
        padding: 0.5rem 0.75rem;
    }

    .nav-link {
        display: flex;
        align-items: center;
        padding: 0.75rem 1rem;
        margin: 0.125rem 0.75rem;
        border-radius: 0.5rem;
        color: rgba(255,255,255,0.7);
        text-decoration: none;
        font-size: 0.875rem;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .nav-link:hover {
        background: rgba(255,255,255,0.1);
        color: #fff;
    }

    .nav-link.active {
        background: var(--accent);
        color: var(--primary-dark);
    }

    .nav-link svg {
        width: 1.25rem;
        height: 1.25rem;
        margin-right: 0.75rem;
        flex-shrink: 0;
    }

    /* Main Content */
    .main-content {
        margin-left: var(--sidebar-width);
        min-height: 100vh;
    }

    /* Top Navigation */
    .top-nav {
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
        padding: 1rem 1.5rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
        position: sticky;
        top: 0;
        z-index: 30;
    }

    /* Page Header */
    .page-header {
        padding: 1.5rem 2rem;
        background: #fff;
        border-bottom: 1px solid #e2e8f0;
    }

    .page-header h1 {
        font-size: 1.5rem;
        font-weight: 700;
        color: var(--primary-dark);
    }

    /* Content Area */
    .content-area {
        padding: 1.5rem 2rem;
    }

    /* Cards */
    .card {
        background: #fff;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px rgba(0,0,0,0.05);
        border: 1px solid #e2e8f0;
    }

    .card-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-body {
        padding: 1.5rem;
    }

    /* Buttons */
    .btn {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        font-weight: 600;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        cursor: pointer;
        text-decoration: none;
        border: none;
    }

    .btn-primary {
        background: var(--primary);
        color: #fff;
    }

    .btn-primary:hover {
        background: var(--primary-light);
    }

    .btn-secondary {
        background: #f1f5f9;
        color: #475569;
        border: 1px solid #e2e8f0;
    }

    .btn-secondary:hover {
        background: #e2e8f0;
    }

    .btn-success {
        background: var(--success);
        color: #fff;
    }

    .btn-danger {
        background: var(--danger);
        color: #fff;
    }

    .btn-warning {
        background: var(--warning);
        color: #fff;
    }

    .btn-sm {
        padding: 0.375rem 0.75rem;
        font-size: 0.75rem;
    }

    .btn-icon {
        padding: 0.5rem;
    }

    /* Form Controls */
    .form-group {
        margin-bottom: 1rem;
    }

    .form-label {
        display: block;
        font-size: 0.875rem;
        font-weight: 600;
        color: #374151;
        margin-bottom: 0.375rem;
    }

    .form-input, .form-select, .form-textarea {
        width: 100%;
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        border: 1px solid #d1d5db;
        border-radius: 0.5rem;
        transition: all 0.2s ease;
        background: #fff;
    }

    .form-input:focus, .form-select:focus, .form-textarea:focus {
        outline: none;
        border-color: var(--primary);
        box-shadow: 0 0 0 3px rgba(30, 58, 95, 0.1);
    }

    .form-error {
        color: var(--danger);
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    .form-hint {
        color: #6b7280;
        font-size: 0.75rem;
        margin-top: 0.25rem;
    }

    /* Tables */
    .table-container {
        overflow-x: auto;
    }

    .table {
        width: 100%;
        border-collapse: collapse;
    }

    .table th {
        text-align: left;
        padding: 0.75rem 1rem;
        font-size: 0.75rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.05em;
        color: #64748b;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .table td {
        padding: 1rem;
        border-bottom: 1px solid #e2e8f0;
        font-size: 0.875rem;
        color: #334155;
    }

    .table tbody tr:hover {
        background: #f8fafc;
    }

    /* Badges */
    .badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        font-size: 0.75rem;
        font-weight: 600;
        border-radius: 9999px;
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

    /* Alerts */
    .alert {
        padding: 1rem 1.25rem;
        border-radius: 0.5rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: flex-start;
    }

    .alert-success {
        background: #d1fae5;
        color: #065f46;
        border-left: 4px solid var(--success);
    }

    .alert-warning {
        background: #fef3c7;
        color: #92400e;
        border-left: 4px solid var(--warning);
    }

    .alert-danger {
        background: #fee2e2;
        color: #991b1b;
        border-left: 4px solid var(--danger);
    }

    .alert-info {
        background: #dbeafe;
        color: #1e40af;
        border-left: 4px solid #3b82f6;
    }

    /* Stat Cards */
    .stat-card {
        background: #fff;
        border-radius: 0.75rem;
        padding: 1.5rem;
        border: 1px solid #e2e8f0;
    }

    .stat-card-icon {
        width: 3rem;
        height: 3rem;
        border-radius: 0.75rem;
        display: flex;
        align-items: center;
        justify-content: center;
        margin-bottom: 1rem;
    }

    .stat-card-value {
        font-size: 1.875rem;
        font-weight: 700;
        color: var(--primary-dark);
    }

    .stat-card-label {
        font-size: 0.875rem;
        color: #64748b;
        margin-top: 0.25rem;
    }

    /* Dropdown */
    .dropdown {
        position: relative;
    }

    .dropdown-menu {
        position: absolute;
        right: 0;
        top: 100%;
        margin-top: 0.5rem;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 0.5rem;
        box-shadow: 0 10px 15px -3px rgba(0,0,0,0.1);
        min-width: 12rem;
        z-index: 50;
        display: none;
    }

    .dropdown-menu.show {
        display: block;
    }

    .dropdown-item {
        display: block;
        padding: 0.625rem 1rem;
        font-size: 0.875rem;
        color: #374151;
        text-decoration: none;
    }

    .dropdown-item:hover {
        background: #f8fafc;
    }

    .dropdown-divider {
        border-top: 1px solid #e2e8f0;
        margin: 0.5rem 0;
    }

    /* Modal */
    .modal-backdrop {
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.5);
        z-index: 50;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .modal {
        background: #fff;
        border-radius: 0.75rem;
        max-width: 32rem;
        width: 100%;
        max-height: 90vh;
        overflow-y: auto;
        margin: 1rem;
    }

    .modal-header {
        padding: 1rem 1.5rem;
        border-bottom: 1px solid #e2e8f0;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .modal-footer {
        padding: 1rem 1.5rem;
        border-top: 1px solid #e2e8f0;
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    /* Mobile Menu Toggle */
    .mobile-menu-btn {
        display: none;
    }

    /* Responsive */
    @media (max-width: 1024px) {
        .sidebar {
            transform: translateX(-100%);
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

        .sidebar-overlay {
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.5);
            z-index: 35;
            display: none;
        }

        .sidebar-overlay.show {
            display: block;
        }
    }

    /* Pagination */
    .pagination {
        display: flex;
        gap: 0.25rem;
        flex-wrap: wrap;
    }

    .pagination a,
    .pagination span {
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 2rem;
        height: 2rem;
        padding: 0 0.5rem;
        font-size: 0.875rem;
        border-radius: 0.375rem;
        text-decoration: none;
    }

    .pagination a {
        color: #374151;
        background: #fff;
        border: 1px solid #e2e8f0;
    }

    .pagination a:hover {
        background: #f8fafc;
    }

    .pagination .active span {
        background: var(--primary);
        color: #fff;
        border: 1px solid var(--primary);
    }

    .pagination .disabled span {
        color: #9ca3af;
        background: #f8fafc;
        border: 1px solid #e2e8f0;
    }

    /* Checkbox styling */
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
        border: 1px solid #e2e8f0;
        border-radius: 0.375rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .checkbox-item:hover {
        background: #f1f5f9;
    }

    .checkbox-item.checked {
        background: #dbeafe;
        border-color: #3b82f6;
    }

    .checkbox-item input[type="checkbox"] {
        margin-right: 0.5rem;
    }

    /* Utilities */
    .text-muted {
        color: #64748b;
    }

    .text-sm {
        font-size: 0.875rem;
    }

    .text-xs {
        font-size: 0.75rem;
    }

    .font-medium {
        font-weight: 500;
    }

    .font-semibold {
        font-weight: 600;
    }

    .mt-4 {
        margin-top: 1rem;
    }

    .mb-4 {
        margin-bottom: 1rem;
    }

    .flex {
        display: flex;
    }

    .items-center {
        align-items: center;
    }

    .justify-between {
        justify-content: space-between;
    }

    .gap-2 {
        gap: 0.5rem;
    }

    .gap-4 {
        gap: 1rem;
    }

    .grid {
        display: grid;
    }

    .grid-cols-2 {
        grid-template-columns: repeat(2, minmax(0, 1fr));
    }

    .grid-cols-3 {
        grid-template-columns: repeat(3, minmax(0, 1fr));
    }

    .grid-cols-4 {
        grid-template-columns: repeat(4, minmax(0, 1fr));
    }

    @media (max-width: 768px) {
        .grid-cols-2, .grid-cols-3, .grid-cols-4 {
            grid-template-columns: 1fr;
        }
    }
</style>
