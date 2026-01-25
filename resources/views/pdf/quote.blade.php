<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Quote {{ $quote->quote_number }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 10pt;
            color: #333;
            line-height: 1.4;
        }
        
        .header {
            margin-bottom: 30px;
            border-bottom: 3px solid {{ $company['primary_color'] ?? '#4F46E5' }};
            padding-bottom: 20px;
        }
        
        .header-content {
            display: table;
            width: 100%;
        }
        
        .header-left {
            display: table-cell;
            width: 60%;
            vertical-align: top;
        }
        
        .header-right {
            display: table-cell;
            width: 40%;
            text-align: right;
            vertical-align: top;
        }
        
        .company-name {
            font-size: 24pt;
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#4F46E5' }};
            margin-bottom: 10px;
        }
        
        .company-info {
            font-size: 9pt;
            color: #666;
            line-height: 1.6;
        }
        
        .quote-title {
            font-size: 28pt;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }
        
        .quote-number {
            font-size: 12pt;
            color: #666;
        }
        
        .info-section {
            margin-bottom: 30px;
        }
        
        .info-row {
            display: table;
            width: 100%;
            margin-bottom: 20px;
        }
        
        .info-column {
            display: table-cell;
            width: 48%;
            vertical-align: top;
        }
        
        .info-column:last-child {
            padding-left: 4%;
        }
        
        .info-box {
            background: #f9fafb;
            padding: 15px;
            border-radius: 4px;
        }
        
        .info-label {
            font-size: 8pt;
            text-transform: uppercase;
            color: #666;
            font-weight: bold;
            margin-bottom: 8px;
        }
        
        .info-content {
            font-size: 10pt;
            line-height: 1.6;
        }
        
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        
        .items-table thead {
            background: {{ $company['primary_color'] ?? '#4F46E5' }};
            color: white;
        }
        
        .items-table th {
            padding: 10px;
            text-align: left;
            font-size: 9pt;
            font-weight: bold;
        }
        
        .items-table th.text-right,
        .items-table td.text-right {
            text-align: right;
        }
        
        .items-table th.text-center,
        .items-table td.text-center {
            text-align: center;
        }
        
        .items-table tbody tr {
            border-bottom: 1px solid #e5e7eb;
        }
        
        .items-table td {
            padding: 10px;
            font-size: 9pt;
        }
        
        .item-name {
            font-weight: bold;
            color: #333;
        }
        
        .item-description {
            font-size: 8pt;
            color: #666;
            margin-top: 3px;
        }
        
        .totals-section {
            margin-top: 20px;
            float: right;
            width: 300px;
        }
        
        .totals-row {
            display: table;
            width: 100%;
            padding: 8px 0;
            border-bottom: 1px solid #e5e7eb;
        }
        
        .totals-row.total {
            border-top: 2px solid #333;
            border-bottom: 3px double #333;
            font-size: 12pt;
            font-weight: bold;
            color: {{ $company['primary_color'] ?? '#4F46E5' }};
            padding: 12px 0;
        }
        
        .totals-label {
            display: table-cell;
            width: 60%;
            text-align: right;
            padding-right: 20px;
            font-size: 9pt;
        }
        
        .totals-value {
            display: table-cell;
            width: 40%;
            text-align: right;
            font-size: 9pt;
        }
        
        .notes-section {
            clear: both;
            margin-top: 40px;
            padding: 15px;
            background: #f9fafb;
            border-left: 4px solid {{ $company['primary_color'] ?? '#4F46E5' }};
        }
        
        .notes-title {
            font-weight: bold;
            margin-bottom: 8px;
            color: #333;
        }
        
        .notes-content {
            font-size: 9pt;
            color: #666;
            white-space: pre-wrap;
        }
        
        .footer {
            margin-top: 40px;
            padding-top: 20px;
            border-top: 2px solid #e5e7eb;
            text-align: center;
            font-size: 8pt;
            color: #666;
        }
        
        .validity {
            background: #fef3c7;
            padding: 10px;
            border-radius: 4px;
            margin-bottom: 20px;
            font-size: 9pt;
            text-align: center;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <div class="header">
        <div class="header-content">
            <div class="header-left">
                <div class="company-name">{{ $company['name'] }}</div>
                <div class="company-info">
                    @if($company['address'])
                        {{ $company['address'] }}<br>
                    @endif
                    @if($company['city'] || $company['state'] || $company['postal_code'])
                        {{ $company['city'] }}@if($company['city'] && $company['state']),@endif {{ $company['state'] }} {{ $company['postal_code'] }}<br>
                    @endif
                    @if($company['country'])
                        {{ $company['country'] }}<br>
                    @endif
                    @if($company['phone'])
                        Phone: {{ $company['phone'] }}<br>
                    @endif
                    @if($company['email'])
                        Email: {{ $company['email'] }}<br>
                    @endif
                    @if($company['website'])
                        Web: {{ $company['website'] }}<br>
                    @endif
                    @if($company['tax_id'])
                        Tax ID: {{ $company['tax_id'] }}
                    @endif
                </div>
            </div>
            <div class="header-right">
                <div class="quote-title">QUOTE</div>
                <div class="quote-number">{{ $quote->quote_number }}</div>
            </div>
        </div>
    </div>

    <!-- Validity Notice -->
    @if($quote->valid_until)
        <div class="validity">
            This quote is valid until {{ $quote->valid_until->format('F d, Y') }}
        </div>
    @endif

    <!-- Quote Information -->
    <div class="info-section">
        <div class="info-row">
            <div class="info-column">
                <div class="info-box">
                    <div class="info-label">Bill To</div>
                    <div class="info-content">
                        <strong>{{ $quote->client->name }}</strong><br>
                        @if($quote->client->company)
                            {{ $quote->client->company }}<br>
                        @endif
                        @if($quote->client->email)
                            {{ $quote->client->email }}<br>
                        @endif
                        @if($quote->client->phone)
                            {{ $quote->client->phone }}<br>
                        @endif
                        @if($quote->client->address)
                            {{ $quote->client->address }}<br>
                        @endif
                    </div>
                </div>
            </div>
            <div class="info-column">
                <div class="info-box">
                    <div class="info-label">Quote Details</div>
                    <div class="info-content">
                        <strong>Date:</strong> {{ $quote->created_at->format('F d, Y') }}<br>
                        <strong>Quote Number:</strong> {{ $quote->quote_number }}<br>
                        @if($quote->reference)
                            <strong>Reference:</strong> {{ $quote->reference }}<br>
                        @endif
                        <strong>Prepared By:</strong> {{ $quote->user->name }}<br>
                        <strong>Status:</strong> {{ ucfirst($quote->status) }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($quote->title)
        <h2 style="margin-bottom: 10px; color: #333;">{{ $quote->title }}</h2>
    @endif

    @if($quote->description)
        <div style="margin-bottom: 20px; color: #666; font-size: 9pt;">
            {{ $quote->description }}
        </div>
    @endif

    <!-- Items Table -->
    @if($showPrices)
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 45%;">Item</th>
                    <th class="text-center" style="width: 10%;">Qty</th>
                    <th class="text-right" style="width: 15%;">Unit Price</th>
                    <th class="text-right" style="width: 10%;">Discount</th>
                    <th class="text-right" style="width: 20%;">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->name }}</div>
                            @if($item->description)
                                <div class="item-description">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }} {{ $item->unit }}</td>
                        <td class="text-right">{{ $quote->currency }}{{ number_format($item->unit_price, 2) }}</td>
                        <td class="text-right">
                            @if($item->discount_amount > 0)
                                {{ $quote->currency }}{{ number_format($item->discount_amount, 2) }}
                            @else
                                -
                            @endif
                        </td>
                        <td class="text-right">{{ $quote->currency }}{{ number_format($item->subtotal, 2) }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Totals -->
        <div class="totals-section">
            <div class="totals-row">
                <div class="totals-label">Subtotal:</div>
                <div class="totals-value">{{ $quote->currency }}{{ number_format($quote->subtotal, 2) }}</div>
            </div>
            
            @if($quote->discount_amount > 0)
                <div class="totals-row">
                    <div class="totals-label">Discount:</div>
                    <div class="totals-value">-{{ $quote->currency }}{{ number_format($quote->discount_amount, 2) }}</div>
                </div>
            @endif
            
            @if($quote->tax_amount > 0)
                <div class="totals-row">
                    <div class="totals-label">Tax ({{ $quote->tax_rate }}%):</div>
                    <div class="totals-value">{{ $quote->currency }}{{ number_format($quote->tax_amount, 2) }}</div>
                </div>
            @endif
            
            <div class="totals-row total">
                <div class="totals-label">TOTAL:</div>
                <div class="totals-value">{{ $quote->currency }}{{ number_format($quote->total, 2) }}</div>
            </div>
        </div>
    @else
        <!-- Items without prices -->
        <table class="items-table">
            <thead>
                <tr>
                    <th style="width: 70%;">Item</th>
                    <th class="text-center" style="width: 30%;">Quantity</th>
                </tr>
            </thead>
            <tbody>
                @foreach($quote->items as $item)
                    <tr>
                        <td>
                            <div class="item-name">{{ $item->name }}</div>
                            @if($item->description)
                                <div class="item-description">{{ $item->description }}</div>
                            @endif
                        </td>
                        <td class="text-center">{{ $item->quantity }} {{ $item->unit }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <!-- Notes -->
    @if($showNotes && $quote->notes)
        <div class="notes-section">
            <div class="notes-title">Notes & Terms:</div>
            <div class="notes-content">{{ $quote->notes }}</div>
        </div>
    @endif

    <!-- Footer -->
    <div class="footer">
        <p>Thank you for your business!</p>
        <p>{{ $company['name'] }} | {{ $company['email'] }} | {{ $company['phone'] }}</p>
    </div>
</body>
</html>
