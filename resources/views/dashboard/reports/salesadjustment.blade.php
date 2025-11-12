@extends('layouts.app')

@section('content')
<style>
.table-responsive {
    max-height: 80vh; /* 80% of the screen height */
    overflow-y: auto;
}

.table-responsive > table > thead th,
.custom-card > table > thead th {
    position: -webkit-sticky; /* For Safari */
    position: sticky;
    top: 0;
    z-index: 10;
   /* Inherits the thead's background color */
}

/* This rule targets the footers in BOTH report styles */
.table-responsive > table > tfoot tr,
.custom-card > table > tfoot tr {
    position: -webkit-sticky; /* For Safari */
    position: sticky;
    bottom: 0;
    z-index: 10;
}
</style>
<style>
    /* --- Body & Card --- */
    body {
        background-color: #99ff99 !important;
    }

    .report-title-bar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        color: white;
        padding: 10px;
    }

    .company-name {
        font-size: 24px;
        font-weight: bold;
    }

    .print-btn {
        background-color: white;
        color: #004d00;
        border: none;
        padding: 6px 12px;
        border-radius: 4px;
        font-weight: bold;
        cursor: pointer;
        margin-right: 5px;
    }

    .print-btn:hover {
        background-color: #e6e6e6;
    }

    .card-header {
        background-color: #004d00 !important;
        padding: 20px;
        border-radius: 8px;
        margin-bottom: 20px;
    }

    h4.fw-bold {
        margin: 0;
    }

    table th, table td {
        text-align: center;
        vertical-align: middle;
    }

    .changed {
        color: red !important;
        font-weight: bold;
    }

    /* ================= PRINT STYLES ================= */
@media print {
    @page {
        size: A4 portrait;
        margin: 15mm;
    }

    body {
        background: white !important;
        color: black !important;
        -webkit-print-color-adjust: exact !important;
        print-color-adjust: exact !important;
        font-size: 12px !important;
    }

    body * {
        visibility: hidden;
    }

    .container, .container * {
        visibility: visible;
    }

    .container {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        margin: 0 auto;
    }

    /* Table formatting for print */
    table {
        width: 100% !important;
        border-collapse: collapse !important;
        font-size: 11.5px !important;
        page-break-inside: auto;
    }

    th, td {
        border: 1px solid #444 !important;
        padding: 5px 6px !important;
        text-align: center !important;
        vertical-align: middle !important;
    }

    thead {
        display: table-header-group !important;
        page-break-inside: avoid !important;
        page-break-after: avoid !important;
    }

    tfoot {
        display: table-footer-group !important;
    }

    tr {
        page-break-inside: avoid !important;
        page-break-after: auto !important;
    }

    /* Ensure header and first row stay together */
    thead + tbody tr:first-child {
        page-break-before: avoid !important;
    }

    .table-dark th {
        background-color: #e0e0e0 !important;
        color: #000 !important;
    }

    .card-header {
        background-color: white !important;
        color: black !important;
        text-align: center !important;
        padding: 10px 0 !important;
        border-bottom: 2px solid #444 !important;
    }

    .report-title-bar {
        justify-content: space-between;
        flex-wrap: wrap;
        text-align: center;
    }

    h2.company-name, h4.fw-bold {
        color: black !important;
        margin: 0;
        line-height: 1.2;
    }

    /* Hide unnecessary UI elements */
    .print-btn,
    a[href],
    .btn,
    .d-flex.justify-content-center {
        display: none !important;
    }

    /* Ensure table sits nicely below header */
    .table-responsive {
        margin-top: 10mm;
    }

    html, body {
        height: auto !important;
        overflow: visible !important;
    }
}

</style>

@php
    use Carbon\Carbon;

    if (!function_exists('formatDate')) {
        function formatDate($date) {
            return $date ? Carbon::parse($date)->timezone('Asia/Colombo')->format('Y-m-d H:i') : '-';
        }
    }

    $grouped = $entries->groupBy('code');
@endphp

<div class="container mt-4">
    <div class="card-header text-center">
        <div class="report-title-bar">
            <div>
              @php
    $companyName = \App\Models\Setting::value('CompanyName');
@endphp

<h2 class="company-name">{{ $companyName ?? 'Default Company' }}</h2>

                <h4 class="fw-bold text-white">📦 වෙනස් කිරීම</h4>
            </div>
            <div>
                @php
                    $settingDate = \App\Models\Setting::value('value');
                @endphp
                <span class="right-info">{{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}</span><br>
                <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
                <a href="{{ route('sales-adjustment.export.excel', request()->all()) }}" class="print-btn">📥 Excel</a>
                <a href="{{ route('sales-adjustment.export.pdf', request()->all()) }}" 
                   class="print-btn" style="background-color: #f44336; color: white;">📥 PDF</a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-bordered table-striped table-hover table-sm align-middle text-center" style="font-size: 14px;">
            <thead class="table-dark">
                <tr>
                    <th>විකුණුම්කරු</th>
                    <th>වර්ගය</th>
                    <th>බර</th>
                    <th>මිල</th>
                    <th>මලු</th>
                    <th>මුළු මුදල</th>
                    <th>බිල්පත් අංකය</th>
                    <th>පාරිභෝගික කේතය</th>
                    <th>වර්ගය (type)</th>
                    <th>දිනය සහ වේලාව</th>
                </tr>
            </thead>
           <tbody>
    @forelse ($entries as $entry)
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

    <div class="d-flex justify-content-center mt-3">
       
    </div>
</div>
@endsection

