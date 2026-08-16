<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>@yield('pdf-title', 'Ministry Report')</title>
<style>
* { margin: 0; padding: 0; box-sizing: border-box; }
body { font-family: 'DejaVu Sans', sans-serif; font-size: 9pt; color: #111827; line-height: 1.45; }
@page { margin: 16mm 12mm 20mm; }

.doc-header { margin-bottom: 12px; padding-bottom: 8px; border-bottom: 2.5px solid #1e3a8a; }
.hdr-left  { float: left;  width: 62%; }
.hdr-right { float: right; width: 36%; text-align: right; }
.clear     { clear: both; }
.church-name   { font-size: 12.5pt; font-weight: bold; color: #1e3a8a; }
.ministry-name { font-size: 9.5pt;  color: #4b5563; margin-top: 2px; }
.doc-title { font-size: 12pt; font-weight: bold; color: #111827; }
.doc-meta  { font-size: 7.5pt; color: #6b7280; margin-top: 3px; }

.section-title {
    font-size: 9.5pt; font-weight: bold; color: #1e3a8a;
    border-left: 3px solid #1e3a8a; padding-left: 7px;
    margin: 12px 0 6px;
}

.stats-wrap { width: 100%; margin-bottom: 12px; border-collapse: collapse; }
.stats-wrap td { border: 1px solid #d1d5db; padding: 7px 10px; text-align: center; }
.stat-label { font-size: 7pt; color: #6b7280; text-transform: uppercase; letter-spacing: .3px; }
.stat-value { font-size: 13pt; font-weight: bold; color: #1e3a8a; margin-top: 3px; }
.stat-value.pos { color: #15803d; }
.stat-value.neg { color: #dc2626; }

table.data { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
table.data th {
    background-color: #1e3a8a; color: #fff;
    padding: 5px 7px; text-align: left; font-size: 7.5pt; font-weight: bold;
}
table.data th.r { text-align: right; }
table.data td { padding: 4px 7px; border-bottom: 1px solid #e5e7eb; font-size: 8pt; vertical-align: top; }
table.data td.r { text-align: right; }
table.data td.c { text-align: center; }
table.data tr:nth-child(even) td { background-color: #f8fafc; }
table.data .total td { font-weight: bold; background-color: #eff6ff !important; border-top: 2px solid #1e3a8a; }
table.data .empty td { text-align: center; color: #9ca3af; font-style: italic; padding: 8px; }

.badge { display: inline; padding: 1px 5px; border-radius: 2px; font-size: 7pt; font-weight: bold; }
.badge-g { background: #dcfce7; color: #166534; }
.badge-r { background: #fee2e2; color: #991b1b; }
.badge-y { background: #fef3c7; color: #92400e; }
.badge-b { background: #dbeafe; color: #1e40af; }
.text-pos { color: #15803d; }
.text-neg { color: #dc2626; }
.text-mut { color: #6b7280; }
.fw-b { font-weight: bold; }

.doc-footer {
    position: fixed; bottom: -16mm; left: 0; right: 0;
    font-size: 7pt; color: #9ca3af; text-align: center;
    border-top: 1px solid #e5e7eb; padding-top: 3px;
}
</style>
</head>
<body>

<div class="doc-header">
    <div class="hdr-left">
        <div class="church-name">{{ $churchName ?? config('app.name') }}</div>
        <div class="ministry-name">{{ $ministry->name }}</div>
    </div>
    <div class="hdr-right">
        <div class="doc-title">@yield('pdf-title')</div>
        <div class="doc-meta">@yield('pdf-meta')</div>
    </div>
    <div class="clear"></div>
</div>

@yield('content')

<div class="doc-footer">
    {{ $churchName ?? config('app.name') }} &mdash; {{ $ministry->name }}
    &nbsp;&bull;&nbsp; Generated: {{ now()->format('d M Y, h:i A') }}
</div>

</body>
</html>
