@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99;
    }

    /* ===== PRINT SETTINGS ===== */
    @media print {
        /* Hide everything except the card */
        body * {
            visibility: hidden;
        }

        .custom-card, .custom-card * {
            visibility: visible;
        }

        .custom-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        /* Optional: Change background & text color for print */
        body, .custom-card {
            background-color: white !important;
            color: black !important;
        }
    }

    /* ===== HIGHLIGHT CLASSES ===== */
    .blue-highlight td {
        background-color: #e3f2fd !important; /* light blue */
        color: #1565c0 !important;           /* dark blue text */
        font-weight: bold;
    }

    .red-highlight td {
        background-color: #ffebee !important; /* light red */
        color: #c62828 !important;            /* dark red text */
        font-weight: bold;
    }

    .orange-highlight td {
        background-color: #fff3e0 !important; /* light orange */
        color: #e65100 !important;            /* dark orange text */
        font-weight: bold;
    }

    /* ===== CARD & TABLE STYLES ===== */
    .custom-card {
        background-color: #006400 !important;
        color: white;
        padding: 1rem !important;
    }

    table.table {
        font-size: 0.9rem;
    }

    table.table td, table.table th {
        padding: 0.3rem 0.6rem !important;
        vertical-align: middle;
    }

    .custom-card table {
        background-color: #006400 !important;
        color: white;
    }

    .custom-card table thead, 
    .custom-card table tfoot {
        background-color: #004d00 !important;
        color: white;
    }

    .custom-card table tbody tr:nth-child(odd):not(.blue-highlight):not(.red-highlight):not(.orange-highlight) {
        background-color: #00550088; /* default odd row */
    }

    .custom-card table tbody tr:nth-child(even):not(.blue-highlight):not(.red-highlight):not(.orange-highlight) {
        background-color: transparent;
    }

    /* ===== HEADER BAR ===== */
    .report-title-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 0.5rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .company-name {
        font-weight: 700;
        font-size: 1.5rem;
        color: white;
        margin: 0;
    }

    .report-title-bar h4 {
        margin: 0;
        color: white;
        font-weight: 700;
        white-space: nowrap;
    }

    .right-info {
        color: white;
        font-weight: 600;
        white-space: nowrap;
        font-size: 0.85rem;
    }

    .print-btn {
        background-color: #004d00;
        color: white;
        border: none;
        padding: 0.3rem 0.8rem;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        white-space: nowrap;
        font-size: 0.9rem;
        transition: background-color 0.3s ease;
    }

    .print-btn:hover {
        background-color: #003300;
    }

    /* ===== LEGEND ===== */
    .legend {
        font-size: 0.85rem;
        margin-top: 10px;
        color: white;
    }
    .legend span {
        display: inline-block;
        width: 15px;
        height: 15px;
        margin-right: 5px;
        border: 1px solid #ccc;
        vertical-align: middle;
    }
    .legend .orange-box { background-color: #fff3e0; }
    .legend .blue-box { background-color: #e3f2fd; }
    .legend .red-box { background-color: #ffebee; }
</style>

<div class="container mt-2" style="background-color: #99ff99; min-height: 100vh; padding: 15px;">
    <div class="card custom-card shadow border-0 rounded-3 p-4">
        <div class="report-title-bar">
           @php
    $companyName = \App\Models\Setting::value('CompanyName');
@endphp

<h2 class="company-name">{{ $companyName ?? 'Default Company' }}</h2>

            <h4 class="fw-bold text-white">ණය වාර්තාව</h4>
            @php
                $settingDate = \App\Models\Setting::value('value');
            @endphp

            <span class="right-info">
                {{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}
            </span>
            <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
        </div>

        <div class="card-body p-0">
            @if ($loans->isEmpty())
                <div class="alert alert-info m-3">
                    No loan records found.
                </div>
            @else
               <table class="table table-bordered table-striped table-hover table-sm mb-0">
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
                                <th colspan="4" class="text-end">Grand Total:</th>
                                <th>
                                    {{ number_format($loans->sum('total_amount'), 2) }}
                                </th>
                            </tr>
                        </tfoot>
                    </table>

                <!-- Legend -->
                  <div class="legend mt-4 mb-3">
    <span class="orange-box"></span> Non realized cheques &nbsp;
    <span class="blue-box"></span> Realized cheques &nbsp;
    <span class="red-box"></span> Returned cheques
</div>

<div class="text-start mb-3 mt-3">
    <a href="{{ route('loans.export.pdf') }}" class="btn btn-danger">📥 PDF</a>
    <a href="{{ route('loans.export.excel') }}" class="btn btn-success">📥 Excel</a>
</div>
            @endif
        </div>
    </div>
</div>
@endsection
