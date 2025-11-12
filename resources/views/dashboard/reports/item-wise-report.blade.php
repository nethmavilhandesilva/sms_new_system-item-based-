@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99;
    }

    .custom-card {
        background-color: #006400 !important;
        color: white;
    }

    .custom-card table thead,
    .custom-card table tfoot {
        background-color: #004d00 !important;
        color: white;
    }

    .custom-card table tbody tr:nth-child(odd) {
        background-color: #00800033;
    }

    .report-title-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 1rem;
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
    }

    .print-btn {
        background-color: #004d00;
        color: white;
        border: none;
        padding: 0.4rem 1rem;
        border-radius: 5px;
        cursor: pointer;
        font-weight: 600;
        white-space: nowrap;
        transition: background-color 0.3s ease;
    }

    .print-btn:hover {
        background-color: #003300;
    }

  @media print {
    body * { visibility: hidden; }
    .custom-card, .custom-card * { visibility: visible; }
    .custom-card { position: absolute; left: 0; top: 0; width: 100%; }
    .print-btn,
    .btn-success,
    .btn-danger { display: none !important; }
}

</style>

<div class="container mt-4">
    <div class="card shadow border-0 rounded-3 p-4 custom-card">
        <div class="report-title-bar">
            @php
    $companyName = \App\Models\Setting::value('CompanyName');
@endphp

<h2 class="company-name">{{ $companyName ?? 'Default Company' }}</h2>

            <h4 class="fw-bold text-white">📦 අයිතමය අනුව වාර්තාව</h4>
            @php
                $settingDate = \App\Models\Setting::value('value');
            @endphp
            <span class="right-info">
                {{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}
            </span>
            <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
        </div>

        {{-- Item description --}}
        @if($sales->isNotEmpty())
           <div class="mb-3 text-white">
    <strong>අයිතමය:</strong> {{ $sales->first()->item_name }} 
    (<strong></strong> {{ $sales->first()->item_code }})
</div>

        @endif

        <table class="table table-bordered table-striped table-sm text-center align-middle">
            <thead class="table-dark">
                <tr>
                    <th>බිල් අංකය</th>
                    <th>මලු</th>
                    <th>බර (kg)</th>
                    <th>මිල (Rs/kg)</th>
                    <th>එකතුව (Rs)</th>
                     <th>ගැණුම්කරු</th>
                    <th>GRN අංකය</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $total_packs = 0;
                    $total_weight = 0;
                    $total_amount = 0;
                @endphp

                @foreach($sales as $sale)
                    <tr>
                        <td>{{ $sale->bill_no }}</td>
                        <td class="text-end">{{ $sale->packs }}</td>
                        <td class="text-end">{{ number_format($sale->weight, 2) }}</td>
                        <td class="text-end">{{ number_format($sale->price_per_kg, 2) }}</td>
                        <td class="text-end">{{ number_format($sale->total, 2) }}</td>
                        <td>{{ $sale->customer_code }}</td>
                        <td>{{ $sale->code }}</td>
                    </tr>
                    @php
                        $total_packs += $sale->packs;
                        $total_weight += $sale->weight;
                        $total_amount += $sale->total;
                    @endphp
                @endforeach
            </tbody>

            <tfoot>
                <tr class="table-secondary fw-bold">
                    <td class="text-end">මුළු එකතුව:</td>
                    <td class="text-end">{{ $total_packs }}</td>
                    <td class="text-end">{{ number_format($total_weight, 2) }}</td>
                    <td></td>
                    <td class="text-end">{{ number_format($total_amount, 2) }}</td>
                    <td colspan="2"></td>
                </tr>
            </tfoot>
        </table>

        <div class="mt-3 d-flex gap-2">
            {{-- Excel Download --}}
            <form action="{{ route('report.download', ['reportType' => 'item-wise-report', 'format' => 'excel']) }}" method="POST">
                @csrf
                @foreach ($filters as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-success">⬇️ Download Excel</button>
            </form>

            {{-- PDF Download --}}
            <form action="{{ route('report.download', ['reportType' => 'item-wise-report', 'format' => 'pdf']) }}" method="POST">
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
