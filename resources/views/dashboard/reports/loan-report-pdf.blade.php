<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>ණය වාර්තාව</title>
    <style>
        body {
            font-family: 'NotoSansSinhala', sans-serif;
            background-color: white;
            color: black;
        }

        /* ===== HIGHLIGHT CLASSES ===== */
        .blue-highlight td {
            background-color: #e3f2fd !important;
            color: #1565c0 !important;
            font-weight: bold;
        }

        .red-highlight td {
            background-color: #ffebee !important;
            color: #c62828 !important;
            font-weight: bold;
        }

        .orange-highlight td {
            background-color: #fff3e0 !important;
            color: #e65100 !important;
            font-weight: bold;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            margin-top: 20px;
        }

        table th, table td {
            border: 1px solid #000;
            padding: 6px 8px;
            text-align: center;
        }

        table thead {
            background-color: #004d00;
            color: white;
        }

        table tfoot {
            background-color: #004d00;
            color: white;
        }

        .legend {
            margin-top: 10px;
            font-size: 11px;
        }
        .legend span {
            display: inline-block;
            width: 12px;
            height: 12px;
            margin-right: 5px;
            border: 1px solid #ccc;
            vertical-align: middle;
        }
        .legend .orange-box { background-color: #fff3e0; }
        .legend .blue-box { background-color: #e3f2fd; }
        .legend .red-box { background-color: #ffebee; }
    </style>
</head>
<body>
    @php
        $companyName = \App\Models\Setting::value('CompanyName') ?? 'Default Company';
        $settingDate = \App\Models\Setting::value('value');
    @endphp

    <h2 style="text-align: center;">{{ $companyName }}</h2>
    <h3 style="text-align: center;">ණය වාර්තාව</h3>
    <p style="text-align: right; font-size: 12px;">{{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}</p>

    <table>
        <thead>
            <tr>
                <th>කෙටි නම</th>
                <th>සම්පූර්ණ නම</th>
                <th>දුරකථන අංකය</th>
                <th>ණය සීමාව (Rs.)</th>
                <th>මුදල</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($loans as $loan)
                <tr class="{{ $loan->highlight_color ?? '' }}">
                    <td>{{ $loan->customer_short_name }}</td>
                    <td>{{ $loan->customer_name }}</td>
                    <td>{{ $loan->telephone_no }}</td>
                    <td>Rs. {{ number_format($loan->credit_limit, 2) }}</td>
                    <td>{{ number_format($loan->total_amount, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <th colspan="4" style="text-align: right;">Grand Total:</th>
                <th>{{ number_format($loans->sum('total_amount'), 2) }}</th>
            </tr>
        </tfoot>
    </table>

    <div class="legend">
        <span class="orange-box"></span> Non realized cheques &nbsp;
        <span class="blue-box"></span> Realized cheques &nbsp;
        <span class="red-box"></span> Returned cheques
    </div>
</body>
</html>
