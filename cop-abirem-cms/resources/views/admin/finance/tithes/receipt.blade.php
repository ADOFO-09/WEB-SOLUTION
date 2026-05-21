<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tithe Receipt - {{ $tithe->receipt_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            padding: 20px;
            background: #f5f5f5;
        }
        .receipt {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 30px;
            border: 2px solid #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px dashed #333;
            padding-bottom: 20px;
            margin-bottom: 20px;
        }
        .church-name {
            font-size: 18px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .church-subtitle {
            font-size: 12px;
            color: #666;
        }
        .receipt-title {
            margin-top: 15px;
            font-size: 16px;
            font-weight: bold;
            background: #333;
            color: white;
            padding: 5px 15px;
            display: inline-block;
        }
        .receipt-number {
            margin-top: 10px;
            font-size: 14px;
            color: #666;
        }
        .amount-section {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #ddd;
            margin-bottom: 20px;
        }
        .amount {
            font-size: 32px;
            font-weight: bold;
            color: #2e7d32;
        }
        .amount-label {
            font-size: 12px;
            color: #666;
            text-transform: uppercase;
        }
        .details {
            margin-bottom: 20px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 8px 0;
            border-bottom: 1px dotted #ddd;
        }
        .detail-label {
            color: #666;
            font-size: 13px;
        }
        .detail-value {
            font-weight: 500;
            font-size: 13px;
        }
        .footer {
            text-align: center;
            padding-top: 20px;
            border-top: 2px dashed #333;
            font-size: 11px;
            color: #666;
        }
        .blessing {
            margin-top: 15px;
            font-style: italic;
            font-size: 12px;
        }
        .print-btn {
            display: block;
            width: 100%;
            padding: 10px;
            margin-top: 20px;
            background: #4f46e5;
            color: white;
            border: none;
            cursor: pointer;
            font-size: 14px;
        }
        @media print {
            body { background: white; padding: 0; }
            .receipt { border: none; max-width: 100%; }
            .print-btn { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="church-name">Church of Pentecost</div>
            <div class="church-subtitle">Abirem District</div>
            <div class="receipt-title">TITHE RECEIPT</div>
            <div class="receipt-number">{{ $tithe->receipt_number }}</div>
        </div>

        <div class="amount-section">
            <div class="amount-label">Amount Received</div>
            <div class="amount">{{ $currencySymbol }} {{ number_format($tithe->amount, 2) }}</div>
        </div>

        <div class="details">
            @if($tithe->member)
            <div class="detail-row">
                <span class="detail-label">Received From:</span>
                <span class="detail-value">{{ $tithe->member->full_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Member ID:</span>
                <span class="detail-value">{{ $tithe->member->member_id }}</span>
            </div>
            @if($tithe->member->phone)
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $tithe->member->phone }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Month For:</span>
                <span class="detail-value">{{ $tithe->month_for_formatted }}</span>
            </div>
            @else
            <div class="detail-row">
                <span class="detail-label">Collection Type:</span>
                <span class="detail-value">Session Tithe (Bulk Collection)</span>
            </div>
            @if($tithe->attendanceSession)
            <div class="detail-row">
                <span class="detail-label">Service:</span>
                <span class="detail-value">{{ $tithe->attendanceSession->serviceType->name ?? 'Service' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Service Date:</span>
                <span class="detail-value">{{ $tithe->attendanceSession->service_date->format('F d, Y') }}</span>
            </div>
            @endif
            <div class="detail-row" style="font-size:11px; color:#888;">
                <span class="detail-label">Note:</span>
                <span class="detail-value">Bulk collection — not linked to individual member</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Payment Date:</span>
                <span class="detail-value">{{ $tithe->payment_date->format('F d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $tithe->payment_method)) }}</span>
            </div>
            @if($tithe->payment_reference)
            <div class="detail-row">
                <span class="detail-label">Reference:</span>
                <span class="detail-value">{{ $tithe->payment_reference }}</span>
            </div>
            @endif
        </div>

        <div class="footer">
            <p>Received by: {{ $tithe->recordedBy->name ?? 'Church Secretary' }}</p>
            <p class="blessing">"Bring the whole tithe into the storehouse..." - Malachi 3:10</p>
            <p style="margin-top: 10px;">Thank you for your faithfulness!</p>
        </div>

        <button class="print-btn" onclick="window.print()">Print Receipt</button>
    </div>
</body>
</html>
