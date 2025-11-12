@extends('layouts.app')

@section('content')
<style>
    /* Print settings */
    @media print {
        body * {
            visibility: hidden;
        }
        .printable-area, .printable-area * {
            visibility: visible;
        }
        .printable-area {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
        }
        .print-btn, .btn-success, .btn-danger, .btn-info {
            display: none !important;
        }
        /* Remove scrollbars in print */
        .table-responsive {
            overflow: visible !important;
        }
        /* Fit content to A4 */
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        table {
            page-break-inside: auto;
            width: 100% !important;
        }
        tr {
            page-break-inside: avoid;
            page-break-after: auto;
        }
    }

    /* Normal styles */
    body { background-color: #99ff99 !important; }
    .card { background-color: #004d00 !important; color: white !important; }
    .report-title-bar { text-align: center; padding: 15px 0; position: relative; }
    .report-title-bar .company-name { font-size: 1.8em; margin-bottom: 5px; }
    .report-title-bar .right-info { position: absolute; top: 15px; right: 15px; font-size: 0.9em; }
    .print-btn { position: absolute; top: 15px; left: 15px; background-color: #4CAF50; color: white; border: none; padding: 8px 15px; border-radius: 5px; cursor: pointer; }

    /* Table styling */
    .table { color: white; font-size: 0.85em; }
    .table thead th { background-color: #003300 !important; color: white !important; }
    .item-summary-row { background-color: #005a00 !important; font-weight: bold; }
    .total-row { background-color: #008000 !important; font-weight: bold; }
</style>

<div class="container-fluid py-4 printable-area">
    <div class="card shadow-sm mb-4">
        <div class="card-header text-center" style="background-color: #004d00 !important;">
            <div class="report-title-bar">
                @php
                    $companyName = \App\Models\Setting::value('CompanyName');
                @endphp

                <h2 class="company-name">{{ $companyName ?? 'Default Company' }}</h2>

                <h4 class="fw-bold text-white">📦 විකිුණුම්/බර මත්තෙහි ඉතිරි වාර්තාව</h4>
                @php
                    $settingDate = \App\Models\Setting::value('value');
                @endphp
                <span class="right-info">
                    {{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}
                </span>
                <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
            </div>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-striped table-hover table-bordered">
                    <thead>
                        <tr>
                            <th rowspan="2">වර්ගය</th>
                            <th rowspan="2">price</th>
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
                            $grandTotalOriginalPacks = 0;
                            $grandTotalOriginalWeight = 0;
                            $grandTotalSoldPacks = 0;
                            $grandTotalSoldWeight = 0;
                            $grandTotalSalesValue = 0;
                            $grandTotalRemainingPacks = 0;
                            $grandTotalRemainingWeight = 0;
                            $grandTotalPrice = 0;
                        @endphp

                        @forelse(collect($reportData)->groupBy('grn_code') as $grnCode => $items)
                            @foreach($items->groupBy('item_name')->sortKeys() as $itemName => $itemRecords)
                                @php
                                    $subTotalOriginalPacks = $itemRecords->sum('original_packs');
                                    $subTotalOriginalWeight = $itemRecords->sum('original_weight');
                                    $subTotalSoldPacks = $itemRecords->sum('sold_packs');
                                    $subTotalSoldWeight = $itemRecords->sum('sold_weight');
                                    $subTotalSalesValue = $itemRecords->sum('total_sales_value');
                                    $subTotalRemainingPacks = $itemRecords->sum('remaining_packs');
                                    $subTotalRemainingWeight = $itemRecords->sum('remaining_weight');
                                    $subTotalPrice = $itemRecords->avg('sp');

                                    // Add to grand totals
                                    $grandTotalOriginalPacks += $subTotalOriginalPacks;
                                    $grandTotalOriginalWeight += $subTotalOriginalWeight;
                                    $grandTotalSoldPacks += $subTotalSoldPacks;
                                    $grandTotalSoldWeight += $subTotalSoldWeight;
                                    $grandTotalSalesValue += $subTotalSalesValue;
                                    $grandTotalRemainingPacks += $subTotalRemainingPacks;
                                    $grandTotalRemainingWeight += $subTotalRemainingWeight;
                                    $grandTotalPrice += $subTotalPrice;
                                @endphp
                                <tr class="item-summary-row">
                                    <td><strong>{{ $itemName }} ({{ $grnCode }})</strong></td>
                                    <td><strong>{{ number_format($subTotalPrice, 2) }}</strong></td>
                                    <td><strong>{{ number_format($subTotalOriginalWeight, 2) }}</strong></td>
                                    <td><strong>{{ number_format($subTotalOriginalPacks) }}</strong></td>
                                    <td><strong>{{ number_format($subTotalSoldWeight, 2) }}</strong></td>
                                    <td><strong>{{ number_format($subTotalSoldPacks) }}</strong></td>
                                    <td><strong>Rs. {{ number_format($subTotalSalesValue, 2) }}</strong></td>
                                    <td><strong>{{ number_format($subTotalRemainingWeight, 2) }}</strong></td>
                                    <td><strong>{{ number_format($subTotalRemainingPacks) }}</strong></td>
                                </tr>
                            @endforeach
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">දත්ත නොමැත.</td>
                            </tr>
                        @endforelse

                        <tr class="total-row">
                            <td class="text-end"><strong>සමස්ත එකතුව:</strong></td>
                            <td><strong>{{ number_format($grandTotalPrice, 2) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalOriginalWeight, 2) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalOriginalPacks) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalSoldWeight, 2) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalSoldPacks) }}</strong></td>
                            <td><strong>Rs. {{ number_format($grandTotalSalesValue, 2) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalRemainingWeight, 2) }}</strong></td>
                            <td><strong>{{ number_format($grandTotalRemainingPacks) }}</strong></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="mt-3">
    <a href="{{ route('report.download.grn.sales.overview', ['format' => 'pdf', 'supplier_code' => request('supplier_code')]) }}" 
       class="btn btn-danger btn-sm">📄 Download PDF</a>

    <a href="{{ route('report.download.grn.sales.overview', ['format' => 'excel', 'supplier_code' => request('supplier_code')]) }}" 
       class="btn btn-success btn-sm">📊 Download Excel</a>
</div>
@endsection