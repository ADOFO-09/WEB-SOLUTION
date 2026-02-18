<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Donation Receipt - {{ $donation->receipt_number ?? $donation->reference_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; font-size: 14px; color: #333; background: #f5f5f5; }
        .receipt { max-width: 400px; margin: 20px auto; background: white; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.1); }
        .header { text-align: center; border-bottom: 2px solid #1e3a5f; padding-bottom: 20px; margin-bottom: 20px; }
        .church-name { font-size: 18px; font-weight: bold; color: #1e3a5f; }
        .church-address { font-size: 11px; color: #666; margin-top: 5px; }
        .receipt-title { font-size: 16px; font-weight: bold; margin-top: 15px; color: #333; }
        .receipt-number { font-size: 12px; color: #666; }
        .section { margin: 15px 0; }
        .row { display: flex; justify-content: space-between; padding: 8px 0; border-bottom: 1px dotted #ddd; }
        .row:last-child { border-bottom: none; }
        .label { color: #666; }
        .value { font-weight: 500; text-align: right; }
        .amount-row { background: #f8f9fa; padding: 15px; margin: 20px 0; border-radius: 8px; text-align: center; }
        .amount-label { font-size: 12px; color: #666; }
        .amount-value { font-size: 28px; font-weight: bold; color: #1e3a5f; }
        .footer { text-align: center; margin-top: 30px; padding-top: 20px; border-top: 1px solid #ddd; }
        .footer p { font-size: 11px; color: #666; margin: 3px 0; }
        .thank-you { font-size: 14px; font-weight: bold; color: #1e3a5f; margin-bottom: 10px; }
        .in-kind-box { background: #e3f2fd; padding: 15px; border-radius: 8px; margin: 15px 0; }
        .in-kind-title { font-weight: bold; color: #1565c0; margin-bottom: 5px; }
        @media print {
            body { background: white; }
            .receipt { box-shadow: none; margin: 0; max-width: 100%; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="receipt">
        <div class="header">
            <div class="church-name">Church of Pentecost</div>
            <div class="church-address">Abirem Assembly<br>Abirem, Eastern Region, Ghana</div>
            <div class="receipt-title">DONATION RECEIPT</div>
            <div class="receipt-number">{{ $donation->receipt_number ?? $donation->reference_number }}</div>
        </div>

        <div class="section">
            <div class="row">
                <span class="label">Date</span>
                <span class="value">{{ \Carbon\Carbon::parse($donation->payment_date)->format('F d, Y') }}</span>
            </div>
            <div class="row">
                <span class="label">Received From</span>
                <span class="value">
                    @if($donation->is_anonymous)
                    Anonymous
                    @else
                    {{ $donation->member->full_name ?? $donation->donor_name ?? 'Unknown' }}
                    @endif
                </span>
            </div>
            @if($donation->project)
            <div class="row">
                <span class="label">Project</span>
                <span class="value">{{ $donation->project->name }}</span>
            </div>
            @endif
            @if($donation->donation_type == 'cash')
            <div class="row">
                <span class="label">Payment Method</span>
                <span class="value">{{ ucfirst(str_replace('_', ' ', $donation->payment_method)) }}</span>
            </div>
            @endif
        </div>

        @if($donation->donation_type == 'cash')
        <div class="amount-row">
            <div class="amount-label">Amount Received</div>
            <div class="amount-value">GH₵ {{ number_format($donation->amount, 2) }}</div>
        </div>
        @else
        <div class="in-kind-box">
            <div class="in-kind-title">In-Kind Donation</div>
            <p>{{ $donation->in_kind_description }}</p>
            @if($donation->estimated_value)
            <p style="margin-top: 10px; font-size: 12px; color: #666;">Estimated Value: GH₵ {{ number_format($donation->estimated_value, 2) }}</p>
            @endif
        </div>
        @endif

        <div class="footer">
            <div class="thank-you">Thank You for Your Generosity!</div>
            <p>Your donation supports the work of the church.</p>
            <p>God bless you abundantly.</p>
            <p style="margin-top: 15px;">Recorded by: {{ $donation->recordedBy->name ?? 'System' }}</p>
            <p>Printed: {{ now()->format('M d, Y g:i A') }}</p>
        </div>
    </div>

    <div class="no-print" style="text-align: center; margin: 20px;">
        <button onclick="window.print()" style="padding: 10px 30px; background: #1e3a5f; color: white; border: none; border-radius: 5px; cursor: pointer; font-size: 14px;">
            Print Receipt
        </button>
    </div>
</body>
</html>
