<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Combined Daily Report</title>
    <style>
        /* Base Styles */
        body {
            font-family: Arial, sans-serif;
            background-color: #eef2f7;
            margin: 0;
            padding: 0;
            font-size: 14px;
            color: #333;
        }

        .container {
            width: 100%;
            max-width: 1100px;
            margin: 20px auto;
            padding: 0 15px;
        }

        /* Section Cards & Headers */
        .report-section {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 25px;
            overflow: hidden;
        }

        .report-header {
            background-color: #004d00;
            color: white;
            padding: 20px 25px;
            text-align: center;
            border-radius: 8px 8px 0 0;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }

        .report-header .title {
            display: flex;
            flex-direction: column;
            align-items: flex-start;
        }

        .report-header h2,
        .report-header h4 {
            margin: 0;
            line-height: 1.2;
            color: white;
        }

        .report-header h2 {
            font-size: 22px;
        }

        .report-header h4 {
            font-size: 16px;
            font-weight: normal;
        }

        .report-header .date-info {
            font-size: 13px;
            color: #ccc;
            text-align: right;
        }

        .print-btn {
            background-color: #003300;
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 5px;
            cursor: pointer;
            font-weight: 600;
            transition: background-color 0.3s ease;
        }

        .print-btn:hover {
            background-color: #001a00;
        }

        /* Tables */
        .table-container {
            padding: 20px;
            overflow-x: auto;
        }

        .report-table,
        .compact-table,
        .bill-summary-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
            color: #333;
            margin-top: 15px;
        }

        .report-table th,
        .report-table td,
        .compact-table th,
        .compact-table td,
        .bill-summary-table th,
        .bill-summary-table td {
            padding: 10px;
            border: 1px solid #e0e0e0;
            text-align: center;
            white-space: nowrap;
        }

        .report-table thead th,
        .report-table tfoot td {
            background-color: #003300;
            color: white;
        }

        .report-table tbody tr:nth-of-type(odd) {
            background-color: #f9f9f9;
        }

        .report-table tbody tr:hover {
            background-color: #e6f7ff;
        }

        .item-summary-row td {
            font-weight: bold;
            background-color: #e0e0e0 !important;
        }

        .total-row td {
            font-weight: bold;
            background-color: #008000 !important;
            color: white !important;
        }

        .total-row td:first-child {
            text-align: right;
        }

        .bill-details {
            padding: 20px;
        }

        .bill-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding-bottom: 10px;
            border-bottom: 2px solid #004d00;
            margin-bottom: 15px;
        }

        .bill-header h5 {
            margin: 0;
            color: #004d00;
            font-size: 16px;
        }

        .bill-header .info {
            font-size: 12px;
            color: #666;
            text-align: right;
        }

        .bill-total-row th {
            text-align: right !important;
            font-weight: bold;
        }

        .bill-total-row th:last-child {
            background-color: #e0e0e0;
            color: #333;
        }

        .grand-total {
            text-align: right;
            font-size: 20px;
            font-weight: bold;
            padding: 15px;
            border-top: 3px solid #008000;
            color: #004d00;
            background-color: #f0f0f0;
        }

        /* Sales Adjustments Table */
        .sales-adjustments-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .sales-adjustments-table th,
        .sales-adjustments-table td {
            padding: 8px;
            border: 1px solid #ddd;
            text-align: center;
        }

        .sales-adjustments-table thead th {
            background-color: #333;
            color: white;
        }

        .table-success {
            background-color: #d4edda;
        }

        /* Original */
        .table-warning {
            background-color: #fff3cd;
        }

        /* Updated */
        .table-danger {
            background-color: #f8d7da;
        }

        /* Deleted */
        .changed {
            background-color: #ffc107;
            font-weight: bold;
            color: #333;
        }

        /* Financial Report */
        .financial-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
        }

        .financial-table th,
        .financial-table td {
            border: 1px solid #e0e0e0;
            padding: 10px;
        }

        .financial-table thead th {
            background-color: #004d00;
            color: white;
            font-weight: bold;
        }

        .financial-total-row {
            background-color: #f0f0f0;
            font-weight: bold;
        }

        .financial-net-balance-row,
        .financial-profit-row,
        .financial-damages-row {
            background-color: #eaf8ff;
            font-weight: bold;
        }

        .financial-net-balance-row td:last-child,
        .financial-profit-row td:last-child,
        .financial-damages-row td:last-child {
            font-size: 1.1em;
        }

        /* Loan Report */
        .loan-table th,
        .loan-table td {
            padding: 8px;
            text-align: left;
        }

        .loan-table th:last-child,
        .loan-table td:last-child {
            text-align: right;
        }

        .loan-totals-row {
            background-color: #dff0d8;
            font-weight: bold;
            color: black;
        }

        .loan-net-balance-row {
            background-color: #004d00;
            color: white;
            font-weight: bold;
        }

        /* Utility Classes */
        .text-center {
            text-align: center;
        }

        .text-end {
            text-align: right;
        }

        .fw-bold {
            font-weight: bold;
        }

        .text-muted {
            color: #6c757d;
        }

        .p-4 {
            padding: 1.5rem !important;
        }

        .alert {
            padding: 15px;
            border-radius: 5px;
            margin: 10px 0;
        }

        .alert-info {
            background-color: #e2f4ff;
            color: #004d00;
        }

        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
        }

        .mb-4 {
            margin-bottom: 25px;
        }

        .mt-4 {
            margin-top: 25px;
        }

        /* Print Styles */
        @media print {
            body {
                background-color: #fff !important;
            }

            .report-section {
                box-shadow: none !important;
                border: 1px solid #eee;
            }

            .report-header {
                background-color: #eee !important;
                color: #000 !important;
            }

            .report-header h2,
            .report-header h4,
            .date-info {
                color: #000 !important;
            }

            .print-btn {
                display: none !important;
            }

            .report-table th,
            .report-table td,
            .compact-table th,
            .compact-table td,
            .bill-summary-table th,
            .bill-summary-table td,
            .sales-adjustments-table th,
            .sales-adjustments-table td,
            .financial-table th,
            .financial-table td,
            .loan-table th,
            .loan-table td {
                border-color: #ccc;
            }

            .report-table thead th,
            .report-table tfoot td {
                background-color: #ddd !important;
                color: #000 !important;
            }

            .total-row td {
                background-color: #e0e0e0 !important;
                color: #333 !important;
            }

            .changed {
                background-color: #ffc107 !important;
                color: #333 !important;
            }
        }
    </style>

</head>

<body>

    <div class="container">

        {{-- The original header and structure is retained, but the table content is changed --}}
        <div class="report-section">
            <div class="report-header">
                <div class="title">
                    <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
                    {{-- Updated title to reflect the new report columns --}}
                    <h4>📦 අයිතම මත්තෙහි විකුණුම් වාර්තාව (බර සහ මලු)</h4>
                </div>
                <div class="date-info">
                    {{-- Display current date/time or date range if available --}}
                    <span>
                        @if (isset($startDate) && isset($endDate))
                            {{ $startDate }} සිට {{ $endDate }} දක්වා
                        @else
                            {{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}
                        @endif
                    </span>
                </div>
            </div>

            <div class="table-container">
                <table class="report-table">
                    <thead>
                        <tr>
                            {{-- Updated table headers to reflect the new structure --}}
                            <th>අයිතම කේතය</th>
                            <th>වර්ගය</th>
                            <th>බර (kg)</th>
                            <th>මලු</th>
                            <th>මලු ගාස්තුව (Rs)</th>
                            <th>ශුද්ධ එකතුව (Rs)</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $total_packs = 0;
                            $total_weight = 0;
                            $total_pack_due_cost = 0;
                            $total_net_total = 0;
                            // The final_total (sum of the 'total' column) is passed as $final_total from the controller
                        @endphp

                        @forelse($weightBasedReportData as $item) {{-- Use the updated variable name --}}
                            @php
                                $pack_due = $item->pack_due ?? 0;
                                $packs = $item->packs ?? 0;
                                $weight = $item->weight ?? 0;
                                $item_total = $item->total ?? 0;

                                // Calculations from the getweight logic:
                                $pack_due_cost = $packs * $pack_due; // Cost of the sold packs
                                $net_total = $item_total - $pack_due_cost; // Net sales total after pack cost

                                $total_packs += $packs;
                                $total_weight += $weight;
                                $total_pack_due_cost += $pack_due_cost;
                                $total_net_total += $net_total;
                            @endphp

                            <tr class="item-summary-row">
                                <td>{{ $item->item_code }}</td>
                                <td class="text-start">{{ $item->item_name }}</td>
                                <td class="text-end">{{ number_format($weight, 2) }}</td>
                                <td class="text-end">{{ number_format($packs, 0) }}</td>
                                <td class="text-end">{{ number_format($pack_due_cost, 2) }}</td>
                                <td class="text-end">Rs. {{ number_format($net_total, 2) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">දත්ත නොමැත.</td>
                            </tr>
                        @endforelse

                        <tr class="total-row">
                            {{-- Total Row for aggregated values (Weight, Packs, Pack Due Cost, Net Total) --}}
                            <td colspan="2" class="text-end fw-bold">මුළු එකතුව:</td>
                            <td class="text-end fw-bold">{{ number_format($total_weight, 2) }}</td>
                            <td class="text-end fw-bold">{{ number_format($total_packs, 0) }}</td>
                            <td class="text-end fw-bold">{{ number_format($total_pack_due_cost, 2) }}</td>
                            <td class="text-end fw-bold">Rs. {{ number_format($total_net_total, 2) }}</td>
                        </tr>

                        {{-- Final Total Row (This is the sum of the original 'total' column from the database) --}}
                        @if (isset($final_total))
                            <tr class="total-row">
                                <td colspan="5" class="text-end fw-bold">අවසන් මුළු එකතුව (විකුණුම් වටිනාකම):</td>
                                <td class="text-end fw-bold">Rs. {{ number_format($final_total, 2) }}</td>
                            </tr>
                        @endif

                    </tbody>
                </table>
            </div>
        </div>

        {{-- Section 2 - GRN Report --}}
        <div class="report-section">
            <div class="report-header">
                <div class="title">
                    <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
                    <h4>📦 විකුණුම්/බර මත්තෙහි ඉතිරි වාර්තාව (GRN)</h4>
                </div>
                <div class="date-info">
                    <span>{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>

                </div>
            </div>
            <div class="table-container">
                <table class="report-table">
                    <thead>
                        <tr>
                            <th rowspan="2">වර්ගය</th>
                            <th colspan="2">මිලදී ගැනීම</th>
                            <th colspan="2">විකුණුම්</th>
                            <th rowspan="2">එකතුව</th>
                            <th colspan="2">ඉතිරි</th>
                        </tr>
                        <tr>
                            <th>බර</th>
                            <th>මලු</th>
                            <th>බර</th>
                            <th>මලු</th>
                            <th>බර</th>
                            <th>මලු</th>

                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $grnGrandTotalOriginalWeight = 0;
                            $grnGrandTotalOriginalPacks = 0;
                            $grnGrandTotalSoldWeight = 0;
                            $grnGrandTotalSoldPacks = 0;

                            $grnGrandTotalSalesValue = 0;
                            $grnGrandTotalRemainingWeight = 0;
                            $grnGrandTotalRemainingPacks = 0;

                        @endphp
              @forelse($dayStartReportData as $item)
                @php
                    $grnGrandTotalOriginalWeight += $item['original_weight'];
                    $grnGrandTotalOriginalPacks += $item['original_packs'];
                    $grnGrandTotalSoldWeight += $item['sold_weight'];
                    $grnGrandTotalSoldPacks += $item['sold_packs'];

                    $grnGrandTotalSalesValue += $item['total_sales_value'];
                    $grnGrandTotalRemainingWeight += $item['remaining_weight'];
                    $grnGrandTotalRemainingPacks += $item['remaining_packs'];

                @endphp
                <tr>
    <td>
        {{ $item['item_name'] }} 
        @if(isset($item['grn_code']))
            ({{ $item['grn_code'] }})
        @endif
    </td>
    <td>{{ number_format($item['original_weight'], 2) }}</td>
    <td>{{ number_format($item['original_packs']) }}</td>
    <td>{{ number_format($item['sold_weight'], 2) }}</td>
    <td>{{ number_format($item['sold_packs']) }}</td>
    <td>Rs. {{ number_format($item['total_sales_value'], 2) }}</td>
    <td>{{ number_format($item['remaining_weight'], 2) }}</td>
    <td>{{ number_format($item['remaining_packs']) }}</td>
</tr>

            @empty
                            <tr>
                                <td colspan="8" class="text-center text-muted py-4">GRN දත්ත නොමැත.</td>
                            </tr>
                        @endforelse
                        <tr class="total-row">
                            <td>සමස්ත එකතුව:</td>
                            <td>{{ number_format($grnGrandTotalOriginalWeight, 2) }}</td>
                            <td>{{ number_format($grnGrandTotalOriginalPacks) }}</td>
                            <td>{{ number_format($grnGrandTotalSoldWeight, 2) }}</td>
                            <td>{{ number_format($grnGrandTotalSoldPacks) }}</td>

                            <td>Rs. {{ number_format($grnGrandTotalSalesValue, 2) }}</td>
                            <td>{{ number_format($grnGrandTotalRemainingWeight, 2) }}</td>
                            <td>{{ number_format($grnGrandTotalRemainingPacks) }}</td>

                        </tr>
                    </tbody>
                </table>
            </div>
        </div>




        {{-- Section 6 - 📦 වෙනස් කිරීම (Changes Report) --}}
        <div class="report-section">
            <div class="report-header">
                <div class="title">
                    <h2 class="company-name">TGK ට්‍රේඩර්ස්</h2>
                    <h4>📦 වෙනස් කිරීම</h4>
                </div>
                <div class="date-info">
                    <span>{{ \Carbon\Carbon::now()->format('Y-m-d H:i') }}</span>

                </div>
            </div>
            <div class="table-container">
                <table class="sales-adjustments-table">
                    <thead>
                        <tr>
                            <th>විකුණුම්කරු</th>
                            <th>මලු</th>
                            <th>වර්ගය</th>
                            <th>බර</th>
                            <th>මිල</th>
                            <th>මුළු මුදල</th>
                            <th>බිල්පත් අංකය</th>
                            <th>පාරිභෝගික කේතය</th>
                            <th>වර්ගය (type)</th>
                            <th>දිනය සහ වේලාව</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($salesadjustments as $entry)
                                    <tr class="@if($entry->type == 'original') table-success 
                                       @elseif($entry->type == 'updated') table-warning 
                               @elseif($entry->type == 'deleted') table-danger 
                               @endif">
                                        <td>{{ $entry->code }}</td>
                                        <td>{{ $entry->item_name }}</td>

                                        {{-- Highlighted columns for updated records --}}
                                        <td @if($entry->type == 'updated') style="color: orange; font-weight:bold;" @endif>
                                            {{ $entry->weight }}
                                        </td>
                                        <td @if($entry->type == 'updated') style="color: orange; font-weight:bold;" @endif>
                                            {{ number_format($entry->price_per_kg, 2) }}
                                        </td>
                                        <td @if($entry->type == 'updated') style="color: orange; font-weight:bold;" @endif>
                                            {{ $entry->packs }}
                                        </td>
                                        <td @if($entry->type == 'updated') style="color: orange; font-weight:bold;" @endif>
                                            {{ number_format($entry->total, 2) }}
                                        </td>

                                        <td>{{ $entry->bill_no }}</td>
                                        <td>{{ strtoupper($entry->customer_code) }}</td>
                                        <td>{{ $entry->type }}</td>
                                        <td>
                                            @if($entry->type == 'original')
                                                                    {{ \Carbon\Carbon::parse($entry->original_created_at)
                                                ->timezone('Asia/Colombo')
                                                ->format('Y-m-d H:i:s') }}
                                            @else
                                                {{ $entry->Date }}
                                                {{ \Carbon\Carbon::parse($entry->created_at)->setTimezone('Asia/Colombo')->format('H:i:s') }}
                                            @endif
                                        </td>
                                    </tr>
                        @empty
                            <tr>
                                <td colspan="10" class="text-center">සටහන් කිසිවක් සොයාගෙන නොමැත</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>



        <style>
            .custom-card {
                border-radius: 12px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                background-color: #ffffff;
                overflow: hidden;
                margin: 20px auto;
                max-width: 800px;
                font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            }

            .report-title-bar {
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 15px 20px;
                background: linear-gradient(90deg, #4b79a1, #283e51);
                color: #fff;
                border-bottom: 2px solid #ccc;
            }

            .company-name {
                font-size: 1.8rem;
                font-weight: bold;
                margin: 0;
            }

            .fw-bold {
                margin: 0;
                font-size: 1.1rem;
            }

            .right-info {
                font-size: 0.95rem;
                background-color: rgba(255, 255, 255, 0.2);
                padding: 3px 8px;
                border-radius: 5px;
            }

            .print-btn {
                background-color: #ff9800;
                color: #fff;
                border: none;
                padding: 6px 12px;
                border-radius: 5px;
                cursor: pointer;
                font-size: 0.9rem;
                transition: background 0.3s;
            }

            .print-btn:hover {
                background-color: #e68900;
            }

            table {
                width: 100%;
                border-collapse: collapse;
                margin-top: 15px;
            }

            thead th {
                font-size: 1rem;
                text-align: left;
                padding: 10px;
            }

            tbody td {
                padding: 10px;
                font-size: 0.95rem;
            }

            tfoot th,
            tfoot td {
                font-size: 1rem;
                font-weight: 600;
                padding: 10px;
            }
        </style>





</body>

</html>