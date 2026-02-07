<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Payment Voucher - {{ $expense->voucher_number ?? $expense->reference_number }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, sans-serif; padding: 20px; background: #f5f5f5; }
        .voucher { max-width: 600px; margin: 0 auto; background: white; padding: 30px; border: 2px solid #333; }
        .header { text-align: center; border-bottom: 2px solid #333; padding-bottom: 15px; margin-bottom: 20px; }
        .church-name { font-size: 18px; font-weight: bold; text-transform: uppercase; }
        .church-subtitle { font-size: 12px; color: #666; }
        .voucher-title { margin-top: 10px; font-size: 16px; font-weight: bold; background: #dc2626; color: white; padding: 5px 20px; display: inline-block; letter-spacing: 2px; }
        .voucher-number { margin-top: 8px; font-size: 14px; color: #666; }
        .details { margin: 20px 0; }
        .detail-row { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px dotted #ddd; }
        .detail-label { color: #666; font-size: 13px; font-weight: 500; }
        .detail-value { font-weight: 600; font-size: 13px; text-align: right; }
        .amount-box { text-align: center; background: #fef2f2; border: 2px solid #dc2626; padding: 15px; margin: 20px 0; }
        .amount { font-size: 28px; font-weight: bold; color: #dc2626; }
        .signatures { display: flex; justify-content: space-between; margin-top: 40px; padding-top: 20px; }
        .sig-block { text-align: center; width: 40%; }
        .sig-line { border-top: 1px solid #333; margin-top: 40px; padding-top: 5px; font-size: 12px; color: #666; }
        .footer { text-align: center; margin-top: 20px; font-size: 11px; color: #999; border-top: 1px solid #eee; padding-top: 10px; }
        .print-btn { display: block; width: 100%; padding: 10px; margin-top: 20px; background: #4f46e5; color: white; border: none; cursor: pointer; font-size: 14px; }
        @media print { body { background: white; padding: 0; } .voucher { border: none; } .print-btn { display: none; } }
    </style>
</head>
<body>
    <div class="voucher">
        <div class="header">
            <div class="church-name">Church of Pentecost</div>
            <div class="church-subtitle">Abirem District</div>
            <div class="voucher-title">PAYMENT VOUCHER</div>
            <div class="voucher-number">{{ $expense->voucher_number ?? $expense->reference_number }}</div>
        </div>

        <div class="amount-box">
            <div style="font-size: 12px; color: #666; text-transform: uppercase;">Amount</div>
            <div class="amount">GH₵ {{ number_format($expense->amount, 2) }}</div>
        </div>

        <div class="details">
            <div class="detail-row">
                <span class="detail-label">Paid To:</span>
                <span class="detail-value">{{ $expense->payee_name }}</span>
            </div>
            @if($expense->payee_phone)
            <div class="detail-row">
                <span class="detail-label">Phone:</span>
                <span class="detail-value">{{ $expense->payee_phone }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Category:</span>
                <span class="detail-value">{{ $expense->expenseCategory->name ?? 'N/A' }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Description:</span>
                <span class="detail-value">{{ Str::limit($expense->description, 60) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Date:</span>
                <span class="detail-value">{{ $expense->expense_date->format('F d, Y') }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Payment Method:</span>
                <span class="detail-value">{{ ucfirst(str_replace('_', ' ', $expense->payment_method)) }}</span>
            </div>
            @if($expense->payment_reference)
            <div class="detail-row">
                <span class="detail-label">Reference:</span>
                <span class="detail-value">{{ $expense->payment_reference }}</span>
            </div>
            @endif
        </div>

        <div class="signatures">
            <div class="sig-block">
                <div class="sig-line">Requested By<br>{{ $expense->requestedBy->name ?? '' }}</div>
            </div>
            <div class="sig-block">
                <div class="sig-line">Approved By<br>{{ $expense->approvedBy->name ?? '' }}</div>
            </div>
        </div>

        <div class="footer">
            Printed: {{ now()->format('M d, Y g:i A') }} | Ref: {{ $expense->reference_number }}
        </div>

        <button class="print-btn" onclick="window.print()">Print Voucher</button>
    </div>
</body>
</html>
