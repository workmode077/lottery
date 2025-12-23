<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Bill #{{ $bill->id }}</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 9px;
            color: #000;
            padding: 10px 15px;
            width: 100%;
        }
        .header {
            text-align: center;
            margin-bottom: 8px;
            border-bottom: 2px dashed #333;
            padding-bottom: 8px;
        }
        .header h1 {
            font-size: 16px;
            margin-bottom: 2px;
            font-weight: bold;
        }
        .header p {
            font-size: 8px;
        }
        .divider {
            border-top: 1px dashed #999;
            margin: 8px 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 2px 0;
            font-size: 8px;
        }
        .info-table td:first-child {
            font-weight: bold;
            width: 35%;
        }
        .items-table {
            margin-top: 8px;
            font-size: 8px;
        }
        .items-table th {
            border-bottom: 1px solid #333;
            padding: 3px 4px;
            text-align: left;
            font-size: 8px;
            font-weight: bold;
        }
        .items-table td {
            padding: 3px 4px;
            font-size: 8px;
        }
        .items-table .col-no {
            width: 10%;
            text-align: center;
        }
        .items-table .col-type {
            width: 20%;
        }
        .items-table .col-number {
            width: 38%;
            padding-left: 3px;
        }
        .items-table .col-price {
            width: 32%;
            text-align: right;
            padding-right: 3px;
        }
        .badge {
            display: inline-block;
            padding: 1px 3px;
            border-radius: 2px;
            font-size: 6px;
            font-weight: bold;
            white-space: nowrap;
        }
        .badge-super { background-color: #ff4444; color: white; }
        .badge-box { background-color: #ffaa00; color: white; }
        .badge-a { background-color: #4444ff; color: white; }
        .badge-b { background-color: #44ff44; color: #000; }
        .badge-c { background-color: #ff44ff; color: white; }
        .badge-ab { background-color: #00aaff; color: white; }
        .badge-ac { background-color: #aa00ff; color: white; }
        .badge-bc { background-color: #ffaa44; color: white; }
        .summary-table {
            margin-top: 8px;
            padding-top: 8px;
            border-top: 1px solid #333;
            font-size: 8px;
        }
        .summary-table td {
            padding: 2px 0;
        }
        .summary-table td:first-child {
            width: 55%;
            padding-left: 2px;
        }
        .summary-table td:last-child {
            width: 45%;
            text-align: right;
            padding-right: 2px;
        }
        .total-row {
            border-top: 2px solid #000;
            padding-top: 4px !important;
            margin-top: 4px;
            font-weight: bold;
            font-size: 10px;
        }
        .total-row td {
            padding-top: 4px !important;
        }
        .footer {
            margin-top: 12px;
            padding-top: 8px;
            border-top: 2px dashed #333;
            text-align: center;
            font-size: 7px;
            color: #666;
        }
        .footer p {
            margin-bottom: 2px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h4>Bill Receipt</h4>
    </div>

    <table class="info-table" style="padding-left: 5%!important;padding-right: 10%!important;">
        <tr>
            <td>Bill No:</td>
            <td><strong>#{{ str_pad($bill->id, 6, '0', STR_PAD_LEFT) }}</strong></td>
        </tr>
        <tr>
            <td>Customer:</td>
            <td>{{ $bill->customer_name }}</td>
        </tr>
        <tr>
            <td>Game Time:</td>
            <td>{{ \Carbon\Carbon::parse($bill->game->time)->format('h:i A') }}</td>
        </tr>
        <tr>
            <td>Date:</td>
            <td>{{ \Carbon\Carbon::parse($bill->created_at)->timezone('Asia/Kolkata')->format('d M Y, h:i A') }}</td>
        </tr>
        <tr>
            <td>Agent:</td>
            <td>{{ $bill->user->name }}</td>
        </tr>
    </table>

    <div class="divider"></div>

    <table class="items-table" style="padding-left: 5%!important;padding-right: 10%!important;">
        <thead>
            <tr>
                <th class="col-no">#</th>
                <th class="col-type">Type</th>
                <th class="col-number">Number</th>
                <th class="col-price">Price</th>
            </tr>
        </thead>
        <tbody>
            @foreach($bill->items as $index => $item)
            <tr>
                <td class="col-no">{{ $index + 1 }}</td>
                <td class="col-type">
                    <span class="badge badge-{{ strtolower($item->type) }}">{{ $item->type }}</span>
                </td>
                <td class="col-number">{{ $item->number }}</td>
                <td class="col-price">₹{{ number_format($item->price, 0) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <table class="summary-table" style="padding-left: 5%!important;padding-right: 10%!important;">
        <tr>
            <td>Total Items:</td>
            <td>{{ $bill->total_count }}</td>
        </tr>
        <tr>
            <td>Subtotal:</td>
            <td>₹{{ number_format($bill->total_amount, 0) }}</td>
        </tr>
       <tr class="total-row">
            <td><strong>TOTAL AMOUNT:</strong></td>
            <td><strong>₹{{ number_format($bill->total_amount - $bill->reduced_commission, 0) }}</strong></td>
        </tr>
    </table>

    <div class="footer">
        <p>Thank you for your business!</p>
        <p>{{ \Carbon\Carbon::now('Asia/Kolkata')->format('d M Y, h:i A') }} IST</p>
    </div>
</body>
</html>
