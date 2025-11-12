{{-- resources/views/reports/grn_sale_code_report.blade.php --}}

@extends('layouts.app')

@section('content')
<style>
    body { background-color: #99ff99; }
    
    .custom-card {
        background-color: #006400;
        color: white;
        padding: 1.5rem;
    }

    .report-title-bar {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .company-name { font-weight: 700; font-size: 1.5rem; color: white; margin: 0; }
    .report-title-bar h4 { margin: 0; font-weight: 700; white-space: nowrap; color: white; }
    .right-info { color: white; font-weight: 600; white-space: nowrap; }
    .print-btn {
        background-color: #004d00;
        color: white;
        border: none;
        padding: 0.4rem 1rem;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        transition: background-color 0.3s ease;
    }
    .print-btn:hover { background-color: #003300; }

    table.table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.9rem;
        margin-bottom: 0;
    }
    table.table th, table.table td {
        padding: 0.4rem 0.6rem;
        border: 1px solid #004d00;
        vertical-align: middle;
    }
    table.table thead, table.table tfoot { background-color: #004d00; color: white; }
    table.table tbody tr:nth-child(odd) { background-color: #00800033; }

    /* Print Styles */
    /* Print Styles */
@media print {
    body { background-color: #fff !important; color: #000; }
    .custom-card { background-color: #fff !important; color: #000 !important; box-shadow: none !important; border: none !important; }
    .custom-card table { border: 1px solid #ccc; }
    .custom-card table th, .custom-card table td { border: 1px solid #ccc; color: #000; }
    .custom-card table thead, .custom-card table tfoot { background-color: #eee !important; color: #000 !important; }
    .custom-card table tbody tr:nth-child(odd) { background-color: #f9f9f9 !important; }
    .report-title-bar h2, .report-title-bar h4, .right-info { color: #000 !important; }

    /* Hide buttons and export section */
    .print-btn,
    .btn-success,
    .btn-danger,
    .mt-3.d-flex { display: none !important; }

    body * { visibility: hidden; }
    .custom-card, .custom-card * { visibility: visible; }
    .custom-card { position: absolute; top: 0; left: 0; width: 100%; }
}

</style>

<div class="container mt-4">
    <div class="card shadow border-0 rounded-3 custom-card">

        {{-- Report Header --}}
        <div class="report-title-bar">
            @php
    $companyName = \App\Models\Setting::value('CompanyName');
@endphp

<h2 class="company-name">{{ $companyName ?? 'Default Company' }}</h2>

            <h4>📄 GRN කේතය අනුව විකුණුම් වාර්තාව</h4>

            @php $settingDate = \App\Models\Setting::value('value'); @endphp
            <span class="right-info">{{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}</span>

            <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
        </div>

        {{-- Filters Summary --}}
        <div class="mb-3 text-white">
           <span>
    <strong>තෝරාගත් GRN කේතය:</strong> {{ $selectedGrnCode }}
    @if($selectedGrnEntry)
        <span class="ms-2">
            ({{ $selectedGrnEntry->item_code }} - {{ $selectedGrnEntry->item_name }})
        </span>
    @endif
</span>

            @if($startDate && $endDate)
                <span class="ms-3"><strong>දිනයන්:</strong> {{ $startDate }} සිට {{ $endDate }} දක්වා</span>
            @elseif($startDate)
                <span class="ms-3"><strong>ආරම්භ දිනය:</strong> {{ $startDate }}</span>
            @elseif($endDate)
                <span class="ms-3"><strong>අවසන් දිනය:</strong> {{ $endDate }}</span>
            @endif
        </div>

        {{-- Sales Table --}}
        <table class="table table-bordered table-striped table-hover text-center align-middle">
            <thead>
                <tr>
                    <th>🗓️ දිනය</th>
                    <th>බිල් අංකය</th>
                    <th>ගෙණුම්කරු කේතය</th>
                    <th>බර (kg)</th>
                    <th>මිල (1kg)</th>
                    <th>පැක්</th>
                    <th>මුළු මුදල (Rs.)</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_packs = 0;
                    $total_weight = 0;
                    $total_amount = 0;
                @endphp

                @forelse($sales as $sale)
                    <tr>
                        <td>{{ $sale->Date }}</td>
                        <td>{{ $sale->bill_no }}</td>
                        <td>{{ $sale->customer_code }}</td>
                        <td class="text-end">{{ number_format($sale->weight, 2) }}</td>
                        <td class="text-end">{{ number_format($sale->price_per_kg, 2) }}</td>
                        <td class="text-end">{{ $sale->packs }}</td>
                        <td class="text-end fw-bold">{{ number_format($sale->total, 2) }}</td>
                    </tr>
                    @php
                        $total_packs += $sale->packs;
                        $total_weight += $sale->weight;
                        $total_amount += $sale->total;
                    @endphp
                @empty
                    <tr>
                        <td colspan="7" class="text-center text-muted py-3">🚫 වාර්තා නැත</td>
                    </tr>
                @endforelse
            </tbody>
            <tfoot>
                <tr class="table-dark fw-bold">
                    <td colspan="3" class="text-end">මුළු එකතුව:</td>
                    <td class="text-end">{{ number_format($total_weight, 2) }}</td>
                    <td></td>
                    <td class="text-end">{{ $total_packs }}</td>
                    <td class="text-end">{{ number_format($total_amount, 2) }}</td>
                </tr>
            </tfoot>
        </table>

        {{-- Export Buttons --}}
        <div class="mt-3 d-flex gap-2 flex-wrap">
            <form action="{{ route('report.download', ['reportType' => 'grn-sale-code-report', 'format' => 'excel']) }}" method="POST" class="d-inline">
                @csrf
                @foreach ($filters as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-success">⬇️ Download Excel</button>
            </form>

            <form action="{{ route('report.download', ['reportType' => 'grn-sale-code-report', 'format' => 'pdf']) }}" method="POST" class="d-inline">
                @csrf
                @foreach ($filters as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-danger">⬇️ Download PDF</button>
            </form>
        </div>
    </div>
</div>
@endsection
