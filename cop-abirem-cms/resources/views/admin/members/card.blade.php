<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Member Card - {{ $member->full_name }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: #f3f4f6;
            padding: 20px;
        }

        .card-container {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 20px;
        }

        .member-card {
            width: 3.375in;
            height: 2.125in;
            background: linear-gradient(135deg, #4f46e5 0%, #7c3aed 100%);
            border-radius: 12px;
            padding: 15px;
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 25px rgba(0,0,0,0.2);
        }

        .member-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: rgba(255,255,255,0.1);
            border-radius: 50%;
        }

        .card-header {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
            position: relative;
            z-index: 1;
        }

        .church-logo {
            width: 40px;
            height: 40px;
            background: white;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            color: #4f46e5;
            font-size: 12px;
            margin-right: 10px;
        }

        .church-name {
            font-size: 10px;
            font-weight: 600;
            line-height: 1.2;
        }

        .card-body {
            display: flex;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .photo-section {
            flex-shrink: 0;
        }

        .member-photo {
            width: 60px;
            height: 60px;
            border-radius: 8px;
            background: white;
            overflow: hidden;
            border: 2px solid rgba(255,255,255,0.5);
        }

        .member-photo img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .member-photo .initials {
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #e0e7ff;
            color: #4f46e5;
            font-weight: bold;
            font-size: 20px;
        }

        .info-section {
            flex: 1;
        }

        .member-name {
            font-size: 14px;
            font-weight: 700;
            margin-bottom: 3px;
        }

        .member-id {
            font-size: 11px;
            font-family: monospace;
            background: rgba(255,255,255,0.2);
            padding: 2px 6px;
            border-radius: 4px;
            display: inline-block;
            margin-bottom: 5px;
        }

        .member-details {
            font-size: 9px;
            opacity: 0.9;
            line-height: 1.4;
        }

        .qr-section {
            position: absolute;
            bottom: 10px;
            right: 10px;
            background: white;
            padding: 5px;
            border-radius: 6px;
        }

        .qr-section svg {
            width: 50px;
            height: 50px;
        }

        /* Back of card */
        .card-back {
            width: 3.375in;
            height: 2.125in;
            background: white;
            border-radius: 12px;
            padding: 15px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        }

        .card-back h4 {
            font-size: 10px;
            color: #4f46e5;
            margin-bottom: 8px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .card-back p {
            font-size: 8px;
            color: #6b7280;
            line-height: 1.4;
            margin-bottom: 6px;
        }

        .card-back .contact {
            margin-top: 10px;
            padding-top: 8px;
            border-top: 1px solid #e5e7eb;
        }

        .card-back .contact p {
            margin-bottom: 3px;
        }

        .print-button {
            background: #4f46e5;
            color: white;
            border: none;
            padding: 12px 24px;
            border-radius: 8px;
            font-size: 14px;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .print-button:hover {
            background: #4338ca;
        }

        @media print {
            body {
                background: white;
                padding: 0;
            }

            .print-button, .back-link {
                display: none !important;
            }

            .card-container {
                page-break-inside: avoid;
            }

            .member-card, .card-back {
                box-shadow: none;
                margin: 0;
            }
        }
    </style>
</head>
<body>
    <div class="card-container">
        <!-- Front of Card -->
        <div class="member-card">
            <div class="card-header">
                <div class="church-logo">COP</div>
                <div class="church-name">
                    THE CHURCH OF PENTECOST<br>
                    ABIREM CENTRAL ASSEMBLY
                </div>
            </div>

            <div class="card-body">
                <div class="photo-section">
                    <div class="member-photo">
                        @if($member->photo_path)
                            <img src="{{ $member->photo_url }}" alt="{{ $member->full_name }}">
                        @else
                            <div class="initials">
                                {{ substr($member->first_name, 0, 1) }}{{ substr($member->last_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                </div>

                <div class="info-section">
                    <div class="member-name">{{ $member->full_name }}</div>
                    <div class="member-id">{{ $member->member_id }}</div>
                    <div class="member-details">
                        Phone: {{ $member->phone_primary }}<br>
                        Joined: {{ $member->date_joined?->format('M Y') }}<br>
                        Status: {{ ucfirst($member->membership_status) }}
                    </div>
                </div>
            </div>

            <div class="qr-section">
                @php
                    $qrData = json_encode($member->getQrCodeData());
                @endphp
                {!! QrCode::size(50)->margin(0)->generate($qrData) !!}
            </div>
        </div>

        <!-- Back of Card -->
        <div class="card-back">
            <h4>Member Information Card</h4>
            <p>
                This card certifies that the holder is a member in good standing of
                {{ \App\Helpers\SettingHelper::churchName() }}.
            </p>
            <p>
                If found, please return to the church address below or contact us.
            </p>

            <div class="contact">
                <p><strong>The Church of Pentecost</strong></p>
                <p>Abirem Central Assembly</p>
                <p>Abirem, Eastern Region, Ghana</p>
                <p>Email: info@copabirem.org</p>
            </div>
        </div>

        <!-- Print Button -->
        <div style="display: flex; gap: 10px; margin-top: 20px;">
            <a href="{{ route('admin.members.show', $member) }}" class="back-link" style="background: #6b7280; color: white; padding: 12px 24px; border-radius: 8px; text-decoration: none; font-size: 14px;">
                ← Back to Profile
            </a>
            <button onclick="window.print()" class="print-button">
                <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/>
                </svg>
                Print Card
            </button>
        </div>
    </div>
</body>
</html>
