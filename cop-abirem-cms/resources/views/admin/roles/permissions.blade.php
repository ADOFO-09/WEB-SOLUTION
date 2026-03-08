@extends('layouts.admin')

@section('title', 'Manage Permissions — ' . $role->name)

@section('header')
<div style="display: flex; justify-content: space-between; align-items: center;">
    <div>
        <h1 style="margin: 0;">Manage Permissions</h1>
        <p style="margin: 0.25rem 0 0; color: #64748b; font-size: 0.9rem;">
            Role: <strong>{{ $role->name }}</strong>
            @if($role->is_system && $role->slug === 'admin')
                <span style="display: inline-flex; align-items: center; gap: 4px; background: #fef3c7; color: #92400e; border: 1px solid #fcd34d; border-radius: 4px; padding: 2px 8px; font-size: 0.75rem; margin-left: 0.5rem;">
                    <svg style="width:12px;height:12px;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126zM12 15.75h.007v.008H12v-.008z"/></svg>
                    Locked — System Admin has all permissions
                </span>
            @endif
        </p>
    </div>
    <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Back to Roles</a>
</div>
@endsection

@section('content')

@if($role->is_system && $role->slug === 'admin')
{{-- ============================================================ --}}
{{-- SYSTEM ADMIN — Read-only view, no form --}}
{{-- ============================================================ --}}
<div class="alert alert-warning" style="margin-bottom: 1.5rem;">
    <strong>System Administrator</strong> always has every permission. This role cannot be edited.
</div>

<div class="card">
    <div class="card-header">
        <h3 style="font-weight: 600;">All Permissions ({{ $allPermissions->flatten()->count() }})</h3>
    </div>
    <div class="card-body">
        @foreach($allPermissions as $module => $modulePermissions)
        <div class="perm-module" style="margin-bottom: 1.5rem;">
            <h4 class="perm-module-title">{{ ucwords(str_replace(['-', '_', '.'], ' ', $module)) }}</h4>
            <div class="perm-grid">
                @foreach($modulePermissions as $permission)
                <div class="perm-item perm-item--locked">
                    <svg style="width:14px;height:14px;color:#16a34a;flex-shrink:0;" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M4.5 12.75l6 6 9-13.5"/>
                    </svg>
                    <div>
                        <div class="perm-name">{{ $permission->name }}</div>
                        @if($permission->description)
                        <div class="perm-desc">{{ $permission->description }}</div>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @endforeach
    </div>
</div>

@else
{{-- ============================================================ --}}
{{-- EDITABLE ROLE — Form with checkboxes --}}
{{-- ============================================================ --}}
<form method="POST" action="{{ route('admin.roles.permissions.update', $role) }}" id="permissionsForm">
    @csrf
    @method('PUT')

    {{-- Sticky Save Bar --}}
    <div class="perm-save-bar">
        <div style="display: flex; align-items: center; gap: 1rem;">
            <span id="selectedCount" style="font-size: 0.875rem; color: #64748b;">
                <span id="checkedNum">{{ count($rolePermissionIds) }}</span> of {{ $allPermissions->flatten()->count() }} permissions selected
            </span>
            <button type="button" onclick="selectAll()" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">Select All</button>
            <button type="button" onclick="deselectAll()" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;">Deselect All</button>
        </div>
        <button type="submit" class="btn btn-primary">Save Permissions</button>
    </div>

    {{-- Permission Modules --}}
    @foreach($allPermissions as $module => $modulePermissions)
    @php
        $moduleId = 'module-' . str_replace(['.', ' '], '-', $module);
        $moduleChecked = $modulePermissions->filter(fn($p) => in_array($p->id, $rolePermissionIds))->count();
        $moduleTotal   = $modulePermissions->count();
    @endphp

    <div class="card perm-card" style="margin-bottom: 1rem;">
        <div class="perm-card-header" onclick="toggleModule('{{ $moduleId }}')" style="cursor: pointer;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                {{-- Module toggle icon --}}
                <svg id="chevron-{{ $moduleId }}" class="perm-chevron" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 8.25l-7.5 7.5-7.5-7.5"/>
                </svg>
                <h4 class="perm-module-heading">{{ ucwords(str_replace(['-', '_', '.'], ' ', $module)) }}</h4>
                <span class="perm-badge" id="badge-{{ $moduleId }}">{{ $moduleChecked }}/{{ $moduleTotal }}</span>
            </div>
            <div style="display: flex; gap: 0.5rem;" onclick="event.stopPropagation()">
                <button type="button" onclick="selectModule('{{ $moduleId }}')" class="perm-module-btn">All</button>
                <button type="button" onclick="deselectModule('{{ $moduleId }}')" class="perm-module-btn">None</button>
            </div>
        </div>

        <div id="{{ $moduleId }}" class="perm-card-body">
            <div class="perm-grid">
                @foreach($modulePermissions as $permission)
                <label class="perm-checkbox-label {{ in_array($permission->id, $rolePermissionIds) ? 'perm-checkbox-label--checked' : '' }}"
                       data-module="{{ $moduleId }}">
                    <input type="checkbox"
                           name="permissions[]"
                           value="{{ $permission->id }}"
                           class="perm-checkbox"
                           data-module="{{ $moduleId }}"
                           {{ in_array($permission->id, old('permissions', $rolePermissionIds)) ? 'checked' : '' }}
                           onchange="onCheckboxChange(this)">
                    <div class="perm-checkbox-content">
                        <div class="perm-name">{{ $permission->name }}</div>
                        @if($permission->description)
                        <div class="perm-desc">{{ $permission->description }}</div>
                        @endif
                    </div>
                </label>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach

    <div style="margin-top: 1.5rem; display: flex; gap: 0.75rem;">
        <button type="submit" class="btn btn-primary">Save Permissions</button>
        <a href="{{ route('admin.roles.index') }}" class="btn btn-secondary">Cancel</a>
    </div>
</form>
@endif

{{-- ============================================================ --}}
{{-- STYLES --}}
{{-- ============================================================ --}}
<style>
    .perm-save-bar {
        position: sticky;
        top: 0;
        z-index: 20;
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        justify-content: space-between;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
    }
    .perm-card { overflow: hidden; }
    .perm-card-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1rem;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
        user-select: none;
    }
    .perm-card-body { padding: 1rem; }
    .perm-module-heading {
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: capitalize;
        margin: 0;
        color: #1e293b;
    }
    .perm-module-title {
        font-weight: 600;
        font-size: 0.9rem;
        text-transform: capitalize;
        margin: 0 0 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid #e2e8f0;
        color: #1e293b;
    }
    .perm-chevron { width: 16px; height: 16px; color: #64748b; transition: transform 0.2s; }
    .perm-chevron--collapsed { transform: rotate(-90deg); }
    .perm-badge {
        display: inline-block;
        background: #dbeafe;
        color: #1d4ed8;
        border-radius: 9999px;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 1px 7px;
    }
    .perm-badge--full { background: #dcfce7; color: #15803d; }
    .perm-module-btn {
        font-size: 0.72rem;
        padding: 2px 8px;
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        background: #fff;
        color: #475569;
        cursor: pointer;
    }
    .perm-module-btn:hover { background: #f1f5f9; }
    .perm-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
        gap: 0.5rem;
    }
    .perm-checkbox-label {
        display: flex;
        align-items: flex-start;
        gap: 0.6rem;
        padding: 0.6rem 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        cursor: pointer;
        transition: background 0.15s, border-color 0.15s;
        background: #fff;
    }
    .perm-checkbox-label:hover { background: #f8fafc; border-color: #94a3b8; }
    .perm-checkbox-label--checked { background: #eff6ff; border-color: #93c5fd; }
    .perm-checkbox { margin-top: 2px; accent-color: #3b82f6; flex-shrink: 0; }
    .perm-checkbox-content { min-width: 0; }
    .perm-name { font-size: 0.825rem; font-weight: 500; color: #1e293b; }
    .perm-desc { font-size: 0.72rem; color: #64748b; margin-top: 1px; line-height: 1.4; }
    .perm-item--locked {
        display: flex;
        align-items: flex-start;
        gap: 0.5rem;
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1fae5;
        border-radius: 6px;
        background: #f0fdf4;
    }
</style>

{{-- ============================================================ --}}
{{-- JAVASCRIPT --}}
{{-- ============================================================ --}}
<script>
    // ── Collapse / expand a module section ──────────────────────
    function toggleModule(moduleId) {
        const body    = document.getElementById(moduleId);
        const chevron = document.getElementById('chevron-' + moduleId);
        const collapsed = body.style.display === 'none';
        body.style.display    = collapsed ? '' : 'none';
        chevron.classList.toggle('perm-chevron--collapsed', !collapsed);
    }

    // ── Select / deselect all within a module ───────────────────
    function selectModule(moduleId) {
        document.querySelectorAll(`input[data-module="${moduleId}"]`)
            .forEach(cb => { cb.checked = true; updateLabel(cb); });
        updateBadge(moduleId);
        updateTotalCount();
    }

    function deselectModule(moduleId) {
        document.querySelectorAll(`input[data-module="${moduleId}"]`)
            .forEach(cb => { cb.checked = false; updateLabel(cb); });
        updateBadge(moduleId);
        updateTotalCount();
    }

    // ── Global select / deselect ─────────────────────────────────
    function selectAll() {
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.checked = true; updateLabel(cb);
        });
        document.querySelectorAll('[id^="module-"]').forEach(el => {
            updateBadge(el.id);
        });
        updateTotalCount();
    }

    function deselectAll() {
        document.querySelectorAll('.perm-checkbox').forEach(cb => {
            cb.checked = false; updateLabel(cb);
        });
        document.querySelectorAll('[id^="module-"]').forEach(el => {
            updateBadge(el.id);
        });
        updateTotalCount();
    }

    // ── Per-checkbox change handler ──────────────────────────────
    function onCheckboxChange(cb) {
        updateLabel(cb);
        updateBadge(cb.dataset.module);
        updateTotalCount();
    }

    // ── Update the checked/unchecked visual on the label ────────
    function updateLabel(cb) {
        const label = cb.closest('.perm-checkbox-label');
        if (!label) return;
        label.classList.toggle('perm-checkbox-label--checked', cb.checked);
    }

    // ── Update the X/Y badge on a module header ──────────────────
    function updateBadge(moduleId) {
        const all     = document.querySelectorAll(`input[data-module="${moduleId}"]`);
        const checked = document.querySelectorAll(`input[data-module="${moduleId}"]:checked`);
        const badge   = document.getElementById('badge-' + moduleId);
        if (!badge) return;
        badge.textContent = checked.length + '/' + all.length;
        badge.classList.toggle('perm-badge--full', checked.length === all.length && all.length > 0);
    }

    // ── Update the global "X of Y selected" counter ─────────────
    function updateTotalCount() {
        const total   = document.querySelectorAll('.perm-checkbox').length;
        const checked = document.querySelectorAll('.perm-checkbox:checked').length;
        const el = document.getElementById('checkedNum');
        if (el) el.textContent = checked;
    }

    // ── On page load: initialise all badges ─────────────────────
    document.addEventListener('DOMContentLoaded', () => {
        document.querySelectorAll('[id^="module-"]').forEach(el => {
            updateBadge(el.id);
        });
        updateTotalCount();
    });
</script>
@endsection
