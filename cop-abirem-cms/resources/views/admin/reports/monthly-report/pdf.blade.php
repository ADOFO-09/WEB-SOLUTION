<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Monthly Report — {{ $report->period_label }}</title>
    <style>
        /*
         * DomPDF-optimised stylesheet.
         * Key rules:
         *   - No width/max-width on the body container (DomPDF manages the canvas).
         *   - @page controls margins (DomPDF reads this directly).
         *   - table-layout:fixed with col widths in % so DomPDF distributes space correctly.
         *   - Avoid max-width, flexbox, and grid — DomPDF has limited support.
         */

        @page {
            size: A4 portrait;
            margin: 6mm 8mm 6mm 8mm;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: Arial, sans-serif;
            font-size: 9pt;
            color: #000;
            background: #fff;
        }

        /* ── Header ─────────────────────────────────────────── */
        .doc-header    { text-align: center; margin-bottom: 4pt; }
        .doc-header h1 { font-size: 12pt; font-weight: bold; text-transform: uppercase; }
        .doc-header h2 { font-size: 9pt;  font-weight: bold; text-transform: uppercase; margin-top: 2pt; letter-spacing: 1pt; }

        .local-line    { font-size: 9pt; font-weight: bold; margin-top: 3pt; }
        .local-val     { border-bottom: 0.5pt solid #000; display: inline; padding-bottom: 1pt; }

        /* ── Main table ──────────────────────────────────────── */
        table.ft {
            width: 100%;
            border-collapse: collapse;
            table-layout: fixed;
        }

        /* Column widths — must total 100% */
        table.ft col.cc { width: 20%; }  /* category */
        table.ft col.ci { width: 38%; }  /* indicator */
        table.ft col.cs { width: 14%; }  /* sub (gender) */
        table.ft col.cv { width: 28%; }  /* value */

        table.ft td, table.ft th {
            border: 0.4pt solid #666;
            padding: 1.5pt 3pt;
            vertical-align: middle;
            line-height: 1.25;
            font-size: 8.5pt;
            word-wrap: break-word;
        }

        /* Column / row header */
        .th-label { font-weight: bold; text-align: center; background: #fff; }
        .th-month { font-weight: bold; text-align: center; background: #fff; font-size: 9pt; text-transform: uppercase; }

        /* Section dividers */
        .sec-hdr {
            font-weight: bold;
            text-align: center;
            background: #fff;
            border-left: none;
            border-right: none;
            border-top: 0.5pt solid #555;
            border-bottom: 0.5pt solid #555;
            padding: 2pt 0;
            font-size: 9pt;
        }

        .cat  { font-weight: bold; text-align: center; vertical-align: middle; font-size: 8.5pt; }
        .ind  { font-size: 8.5pt; }
        .sub  { font-size: 8.5pt; font-style: italic; }
        .val  { font-size: 8.5pt; text-align: right; font-weight: bold; color: #1a3c6e; padding-right: 4pt; }

        /* Footer */
        .doc-footer {
            margin-top: 5pt;
            font-size: 7.5pt;
            color: #555;
        }
        .doc-footer table { width: 100%; border: none; table-layout: auto; }
        .doc-footer td    { border: none; padding: 0; font-size: 7.5pt; }

        /* Signature block */
        .sig-area { margin-top: 10mm; }
        .sig-area table { width: 100%; border: none; table-layout: auto; }
        .sig-area td     { border: none; text-align: center; font-size: 8pt; padding: 0; }
        .sig-line { display: block; border-bottom: 0.5pt solid #000; width: 55mm; margin: 0 auto 2pt; }
    </style>
</head>
<body>

    {{-- ── Document Header ────────────────────────────────── --}}
    <div class="doc-header">
        <h1>{{ \App\Helpers\SettingHelper::reportHeader() ?: \App\Helpers\SettingHelper::churchName() }}</h1>
        <h2>Local Assembly Monthly Report Form</h2>
        <div class="local-line">
            LOCAL: <span class="local-val">{{ \App\Models\Setting::get('church_name', '') }}</span>
            &nbsp;&nbsp;&nbsp;
            YEAR: <span class="local-val">{{ $report->year }}</span>
        </div>
    </div>

    {{-- ── Main Form Table ─────────────────────────────────── --}}
    <table class="ft">
        <colgroup>
            <col class="cc">
            <col class="ci">
            <col class="cs">
            <col class="cv">
        </colgroup>

        {{-- Table column headers --}}
        <tr>
            <td class="th-label" style="border:0.4pt solid #666;"></td>
            <td class="th-label" colspan="2" style="border:0.4pt solid #666;">Operational Indicators</td>
            <td class="th-month" style="border:0.4pt solid #666;">{{ strtoupper($report->month_name) }}</td>
        </tr>

        {{-- ══ DEMOGRAPHICS ═══════════════════════════════════ --}}
        <tr><td colspan="4" class="sec-hdr">Demographics</td></tr>

        <tr>
            <td class="cat" rowspan="4">Leadership</td>
            <td class="ind" colspan="2">Elders</td>
            <td class="val">{{ $report->elders_count ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Deacons</td>
            <td class="val">{{ $report->deacons_count ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Deaconess</td>
            <td class="val">{{ $report->deaconesses_count ?: '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Leader</td>
            <td class="val">{{ $report->leaders_count ?: '' }}</td>
        </tr>

        {{-- Transfer In --}}
        <tr>
            <td class="cat" rowspan="12">TRANSFER IN<br>(CHILDREN &amp; ADULT)</td>
            <td class="ind" rowspan="3">Children transfer</td>
            <td class="sub">(Male)</td>
            <td class="val">{{ $report->transfer_in_children_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">(Female)</td><td class="val">{{ $report->transfer_in_children_female ?: '' }}</td></tr>
        <tr><td class="sub">total</td><td class="val">{{ ($report->transfer_in_children_male + $report->transfer_in_children_female) ?: '' }}</td></tr>

        <tr>
            <td class="ind" rowspan="3">Teen 13-19yrs</td>
            <td class="sub">Male</td>
            <td class="val">{{ $report->transfer_in_teens_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">Female</td><td class="val">{{ $report->transfer_in_teens_female ?: '' }}</td></tr>
        <tr><td class="sub">total</td><td class="val">{{ ($report->transfer_in_teens_male + $report->transfer_in_teens_female) ?: '' }}</td></tr>

        <tr>
            <td class="ind" rowspan="3">Young Adults (20-35)</td>
            <td class="sub">Male</td>
            <td class="val">{{ $report->transfer_in_young_adults_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">Female</td><td class="val">{{ $report->transfer_in_young_adults_female ?: '' }}</td></tr>
        <tr><td class="sub">total</td><td class="val">{{ ($report->transfer_in_young_adults_male + $report->transfer_in_young_adults_female) ?: '' }}</td></tr>

        <tr>
            <td class="ind" rowspan="3">Other Adults (35 &amp; Above)</td>
            <td class="sub">male</td>
            <td class="val">{{ $report->transfer_in_adults_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">female</td><td class="val">{{ $report->transfer_in_adults_female ?: '' }}</td></tr>
        <tr><td class="sub">total</td><td class="val">{{ ($report->transfer_in_adults_male + $report->transfer_in_adults_female) ?: '' }}</td></tr>

        {{-- Transfer Out --}}
        <tr>
            <td class="cat" rowspan="12">TRANSFER OUT<br>(CHILDREN &amp; ADULT)</td>
            <td class="ind" rowspan="3">Children</td>
            <td class="sub">male</td>
            <td class="val">{{ $report->transfer_out_children_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">Female</td><td class="val">{{ $report->transfer_out_children_female ?: '' }}</td></tr>
        <tr><td class="sub">total</td><td class="val">{{ ($report->transfer_out_children_male + $report->transfer_out_children_female) ?: '' }}</td></tr>

        <tr>
            <td class="ind" rowspan="3">Teen 13-19yrs</td>
            <td class="sub">Male</td>
            <td class="val">{{ $report->transfer_out_teens_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">Female</td><td class="val">{{ $report->transfer_out_teens_female ?: '' }}</td></tr>
        <tr><td class="sub">total</td><td class="val">{{ ($report->transfer_out_teens_male + $report->transfer_out_teens_female) ?: '' }}</td></tr>

        <tr>
            <td class="ind" rowspan="3">Young Adults (20-35)</td>
            <td class="sub">Male</td>
            <td class="val">{{ $report->transfer_out_young_adults_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">Female</td><td class="val">{{ $report->transfer_out_young_adults_female ?: '' }}</td></tr>
        <tr><td class="sub">total</td><td class="val">{{ ($report->transfer_out_young_adults_male + $report->transfer_out_young_adults_female) ?: '' }}</td></tr>

        <tr>
            <td class="ind" rowspan="3">Other Adults (35 &amp; above)</td>
            <td class="sub">male</td>
            <td class="val">{{ $report->transfer_out_adults_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">Female</td><td class="val">{{ $report->transfer_out_adults_female ?: '' }}</td></tr>
        <tr><td class="sub">total</td><td class="val">{{ ($report->transfer_out_adults_male + $report->transfer_out_adults_female) ?: '' }}</td></tr>

        {{-- ══ OPERATIONAL STATS ════════════════════════════════ --}}
        <tr><td colspan="4" class="sec-hdr">Operational Stats</td></tr>

        <tr>
            <td class="cat" rowspan="5">Home cell</td>
            <td class="ind" colspan="2">Opened</td>
            <td class="val">{{ $report->home_cell_opened ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Closed</td><td class="val">{{ $report->home_cell_closed ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Meetings held</td><td class="val">{{ $report->home_cell_meetings_held ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Male attendance</td><td class="val">{{ $report->home_cell_male_attendance ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Female Attendance</td><td class="val">{{ $report->home_cell_female_attendance ?: '' }}</td></tr>

        <tr>
            <td class="cat" rowspan="7">Bible Study groups</td>
            <td class="ind" colspan="2">Opened</td>
            <td class="val">{{ $report->bible_study_opened ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Closed</td><td class="val">{{ $report->bible_study_closed ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Number of leaders</td><td class="val">{{ $report->bible_study_leaders_count ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Meetings held</td><td class="val">{{ $report->bible_study_meetings_held ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Male attendance</td><td class="val">{{ $report->bible_study_male_attendance ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Female attendance</td><td class="val">{{ $report->bible_study_female_attendance ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Public Bible readings</td><td class="val">{{ $report->bible_study_public_readings ?: '' }}</td></tr>

        {{-- ══ OUTREACHES AND SOULS WON ═════════════════════════ --}}
        <tr><td colspan="4" class="sec-hdr">Outreaches and Souls Won</td></tr>

        <tr>
            <td class="cat" rowspan="5">Souls won by Category</td>
            <td class="ind" colspan="2">Total outreaches (rallies, house-to-house, etc)</td>
            <td class="val">{{ $report->total_outreaches ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Adults Souls won (non-CoP)</td><td class="val">{{ $report->souls_adults ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Gospel Sunday Souls</td><td class="val">{{ $report->souls_gospel_sunday ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Children Souls</td><td class="val">{{ $report->souls_children ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Other CoP souls</td><td class="val">{{ $report->souls_other_cop ?: '' }}</td></tr>

        <tr>
            <td class="cat" rowspan="6">Special Ministry Souls</td>
            <td class="ind" colspan="2">HUM souls</td>
            <td class="val">{{ $report->souls_hum ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">MPWD souls</td><td class="val">{{ $report->souls_mpwd ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Chaplaincy Souls</td><td class="val">{{ $report->souls_chaplaincy ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Chieftaincy Souls</td><td class="val">{{ $report->souls_chieftaincy ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">SOM souls</td><td class="val">{{ $report->souls_som ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Digital Space souls</td><td class="val">{{ $report->souls_digital_space ?: '' }}</td></tr>

        <tr>
            <td class="cat" rowspan="2">Follow up &amp;<br>Retention</td>
            <td class="ind" colspan="2">Backsliders won back</td>
            <td class="val">{{ $report->backsliders_won_back ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Backsliders Being Followed</td><td class="val">{{ $report->backsliders_being_followed ?: '' }}</td></tr>

        {{-- ══ WATER BAPTISM AND HOLY SPIRIT BAPTISM ═══════════ --}}
        <tr><td colspan="4" class="sec-hdr">Water Baptism and Holy Spirit Baptism</td></tr>

        <tr>
            <td class="cat" rowspan="8">Water Baptism</td>
            <td class="ind" rowspan="2">Children 13 years</td>
            <td class="sub">(Male)</td>
            <td class="val">{{ $report->water_baptism_children_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">(Female)</td><td class="val">{{ $report->water_baptism_children_female ?: '' }}</td></tr>

        <tr>
            <td class="ind" rowspan="2">Teens 13-19yrs</td>
            <td class="sub">(Male)</td>
            <td class="val">{{ $report->water_baptism_teens_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">(Female)</td><td class="val">{{ $report->water_baptism_teens_female ?: '' }}</td></tr>

        <tr>
            <td class="ind" rowspan="2">Young Adults 20-35yrs</td>
            <td class="sub">Male</td>
            <td class="val">{{ $report->water_baptism_young_adults_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">(Female)</td><td class="val">{{ $report->water_baptism_young_adults_female ?: '' }}</td></tr>

        <tr>
            <td class="ind" rowspan="2">Adults above 35yrs</td>
            <td class="sub">(Male)</td>
            <td class="val">{{ $report->water_baptism_adults_male ?: '' }}</td>
        </tr>
        <tr><td class="sub">(Female)</td><td class="val">{{ $report->water_baptism_adults_female ?: '' }}</td></tr>

        <tr>
            <td class="cat" rowspan="2">Holy Spirit baptism</td>
            <td class="ind" colspan="2">New Converts baptized in H.S</td>
            <td class="val">{{ $report->hs_baptism_new_converts ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Old Members now baptized in H.S</td><td class="val">{{ $report->hs_baptism_old_members ?: '' }}</td></tr>

        <tr>
            <td class="cat" rowspan="5">Life Events</td>
            <td class="ind" colspan="2">Birth</td>
            <td class="val">{{ $report->births ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Male Children Dedicated</td><td class="val">{{ $report->male_children_dedicated ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Female children dedicated</td><td class="val">{{ $report->female_children_dedicated ?: '' }}</td></tr>
        <tr>
            <td class="ind">Death</td>
            <td class="sub">(Children)</td>
            <td class="val">{{ $report->deaths_children ?: '' }}</td>
        </tr>
        <tr><td class="ind"></td><td class="sub">(Adults)</td><td class="val">{{ $report->deaths_adults ?: '' }}</td></tr>

        {{-- ══ WORSHIP AND CHURCH ATTENDANCE ════════════════════ --}}
        <tr><td colspan="4" class="sec-hdr">Worship and Church Attendance</td></tr>

        <tr>
            <td class="cat" rowspan="3">Sunday services</td>
            <td class="ind" colspan="2">Sunday morning attendance</td>
            <td class="val">{{ $report->sunday_morning_attendance ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Communion Sunday attendance</td><td class="val">{{ $report->communion_sunday_attendance ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Communion Participants</td><td class="val">{{ $report->communion_participants ?: '' }}</td></tr>

        <tr>
            <td class="cat" rowspan="4">Classes and teachings</td>
            <td class="ind" colspan="2">New Converts Classes held</td>
            <td class="val">{{ $report->new_converts_classes_held ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">New Converts Class attendance</td><td class="val">{{ $report->new_converts_class_attendance ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">New converts retained</td><td class="val">{{ $report->new_converts_retained ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Presiding Elder visits new converts class</td><td class="val">{{ $report->elder_visits_new_converts ?: '' }}</td></tr>

        <tr>
            <td class="cat" rowspan="4">Mid-Week Service</td>
            <td class="ind" colspan="2">Mid-Week Church teachings</td>
            <td class="val">{{ $report->midweek_teachings ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Mid-Week Church Attendance</td><td class="val">{{ $report->midweek_attendance ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Weekly Prayer meetings</td><td class="val">{{ $report->weekly_prayer_meetings ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Weekly prayer attendance</td><td class="val">{{ $report->weekly_prayer_attendance ?: '' }}</td></tr>

        <tr>
            <td class="cat" rowspan="5">Special Programs</td>
            <td class="ind" colspan="2">Annual Thematic teachings</td>
            <td class="val">{{ $report->annual_thematic_teachings ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Holy Ghost Prayer sessions</td><td class="val">{{ $report->holy_ghost_prayer_sessions ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Marriage teachings</td><td class="val">{{ $report->marriage_teachings ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Blessed marriages</td><td class="val">{{ $report->blessed_marriages ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Intergenerational services</td><td class="val">{{ $report->intergenerational_services ?: '' }}</td></tr>

        {{-- ══ SOCIAL INTERVENTIONS ══════════════════════════════ --}}
        <tr><td colspan="4" class="sec-hdr">Social Interventions</td></tr>

        <tr>
            <td class="cat" rowspan="6">Interventions</td>
            <td class="ind" colspan="2">Tertiary sponsorship</td>
            <td class="val">{{ $report->social_tertiary_sponsorship ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Pre-tertiary sponsorship</td><td class="val">{{ $report->social_pre_tertiary_sponsorship ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Health Support</td><td class="val">{{ $report->social_health_support ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Apprenticeship Support</td><td class="val">{{ $report->social_apprenticeship_support ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Community Transformation</td><td class="val">{{ $report->social_community_transformation ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Environmental care</td><td class="val">{{ $report->social_environmental_care ?: '' }}</td></tr>

        <tr>
            <td class="cat" rowspan="3">Beneficiaries</td>
            <td class="ind" colspan="2">Male Beneficiaries</td>
            <td class="val">{{ $report->beneficiaries_male ?: '' }}</td>
        </tr>
        <tr><td class="ind" colspan="2">Female beneficiaries</td><td class="val">{{ $report->beneficiaries_female ?: '' }}</td></tr>
        <tr><td class="ind" colspan="2">Other beneficiaries</td><td class="val">{{ $report->beneficiaries_other ?: '' }}</td></tr>

        <tr>
            <td class="cat" rowspan="2">Financial Data</td>
            <td class="ind" colspan="2">Amount Spent on Human Development</td>
            <td class="val">{{ $report->amount_spent_human_development > 0 ? number_format($report->amount_spent_human_development, 2) : '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Amount spent on non-human</td>
            <td class="val">{{ $report->amount_spent_non_human > 0 ? number_format($report->amount_spent_non_human, 2) : '' }}</td>
        </tr>

        {{-- ══ TITHES AND OFFERING ═══════════════════════════════ --}}
        <tr><td colspan="4" class="sec-hdr">Tithes and offering</td></tr>

        <tr>
            <td class="cat" rowspan="2"></td>
            <td class="ind" colspan="2">Monthly Net Tithes</td>
            <td class="val">{{ $report->monthly_net_tithes > 0 ? number_format($report->monthly_net_tithes, 2) : '' }}</td>
        </tr>
        <tr>
            <td class="ind" colspan="2">Monthly Missions offering</td>
            <td class="val">{{ $report->monthly_missions_offering > 0 ? number_format($report->monthly_missions_offering, 2) : '' }}</td>
        </tr>

    </table>

    {{-- ── Notes ──────────────────────────────────────────────── --}}
    @if($report->notes)
    <div style="margin-top:4pt; font-size:8.5pt;">
        <strong>Notes:</strong> {{ $report->notes }}
    </div>
    @endif

    {{-- ── Signature Block ────────────────────────────────────── --}}
    <div class="sig-area">
        <table>
            <tr>
                <td style="width:50%; text-align:center;">
                    <span class="sig-line"></span>
                    District Pastor's Signature &amp; Date
                </td>
                <td style="width:50%; text-align:center;">
                    <span class="sig-line"></span>
                    District Overseer's Signature &amp; Date
                </td>
            </tr>
        </table>
    </div>

    {{-- ── Footer ─────────────────────────────────────────────── --}}
    <div class="doc-footer" style="margin-top:6pt;">
        <table>
            <tr>
                <td>Prepared by: {{ $report->creator?->name ?? '—' }}</td>
                <td style="text-align:center;">
                    @if($report->status === 'submitted')
                        Submitted: {{ $report->submitted_at?->format('d M Y') }} by {{ $report->submitter?->name ?? '—' }}
                    @else
                        Status: Draft
                    @endif
                </td>
                <td style="text-align:right;">Printed: {{ now()->format('d M Y H:i') }}</td>
            </tr>
        </table>
    </div>

</body>
</html>
