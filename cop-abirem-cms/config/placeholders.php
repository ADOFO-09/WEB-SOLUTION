<?php

/*
|--------------------------------------------------------------------------
| Placeholder Registry — Single Source of Truth
|--------------------------------------------------------------------------
|
| Every supported {placeholder} is defined here with its type, UI label,
| and resolution source. Adding a new placeholder = one entry here.
|
| Types:
|   recipient — resolved from the recipient Member model (per-recipient)
|   manual    — entered once by the sender at compose time
|   system    — auto-resolved from church settings or app context
|
| Entries marked alias:true are backwards-compatible aliases for old
| templates. They resolve identically to their canonical counterpart but
| are hidden in the Insert Placeholder UI to avoid clutter.
|
*/

return [

    // ── Type A: Recipient ──────────────────────────────────────────────
    // Differ per recipient; resolved from the Member model at send time.

    'member_name' => ['type' => 'recipient', 'label' => 'Member Name', 'source' => 'full_name'],
    'first_name'  => ['type' => 'recipient', 'label' => 'First Name',  'source' => 'first_name'],
    'last_name'   => ['type' => 'recipient', 'label' => 'Last Name',   'source' => 'last_name'],
    'phone'       => ['type' => 'recipient', 'label' => 'Phone',       'source' => 'phone_primary'],
    'member_id'   => ['type' => 'recipient', 'label' => 'Member ID',   'source' => 'member_id'],
    'title'       => ['type' => 'recipient', 'label' => 'Title',       'source' => 'title'],

    // Alias: old templates used {name} — resolves identically to {member_name}
    'name'        => ['type' => 'recipient', 'label' => 'Member Name', 'source' => 'full_name', 'alias' => true],

    // ── Type B: Manual ────────────────────────────────────────────────
    // Entered once by the sender at compose time; consistent across all
    // recipients in the same send batch.

    'service_name' => ['type' => 'manual', 'label' => 'Service Name'],
    'date'         => ['type' => 'manual', 'label' => 'Date'],
    'time'         => ['type' => 'manual', 'label' => 'Time'],
    'theme'        => ['type' => 'manual', 'label' => 'Theme'],
    'venue'        => ['type' => 'manual', 'label' => 'Venue'],
    'amount'       => ['type' => 'manual', 'label' => 'Amount'],
    'event'        => ['type' => 'manual', 'label' => 'Event Name'],

    // ── Type C: System ────────────────────────────────────────────────
    // Auto-generated at resolve time; no manual input from the sender.
    // Church branding values are editable in Settings → General so the
    // same installation can be reused by any congregation.

    'church_name'    => ['type' => 'system', 'label' => 'Church Name',    'source' => 'setting:church_name'],
    'church_phone'   => ['type' => 'system', 'label' => 'Church Phone',   'source' => 'setting:church_phone'],
    'church_email'   => ['type' => 'system', 'label' => 'Church Email',   'source' => 'setting:church_email'],
    'church_address' => ['type' => 'system', 'label' => 'Church Address', 'source' => 'setting:church_address'],
    'pastor_name'    => ['type' => 'system', 'label' => 'Pastor Name',    'source' => 'setting:pastor_name'],
    'sender_name'    => ['type' => 'system', 'label' => 'Sender Name',    'source' => 'auth_user_name'],
    'current_date'   => ['type' => 'system', 'label' => "Today's Date",   'source' => 'now:date'],
    'current_year'   => ['type' => 'system', 'label' => 'Current Year',   'source' => 'now:year'],
    'current_time'   => ['type' => 'system', 'label' => 'Current Time',   'source' => 'now:time'],

    // Alias: old templates used {church} — resolves identically to {church_name}
    'church'         => ['type' => 'system', 'label' => 'Church Name',    'source' => 'setting:church_name', 'alias' => true],

];
