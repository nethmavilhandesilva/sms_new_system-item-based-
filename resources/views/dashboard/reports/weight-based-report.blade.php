@extends('layouts.app')

@section('content')
<style>
.compact-table thead th {
    position: -webkit-sticky; /* For Safari */
    position: sticky;
    top: 0;
    z-index: 10;
}
.compact-table tfoot tr {
    position: -webkit-sticky; /* For Safari */
    position: sticky;
    bottom: 0;
    z-index: 10;
}
</style>
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
            transition: background-color 0.3s ease;
        }

        .print-btn:hover {
            background-color: #003300;
        }

        .compact-table th,
        .compact-table td {
            font-size: 13px;
            padding: 4px 8px;
        }

        @media print {
            body {
                background-color: #fff !important;
                color: #000;
            }

            .custom-card {
                background-color: #fff !important;
                color: #000 !important;
                box-shadow: none !important;
                border: none !important;
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .custom-card table {
                border: 1px solid #ccc;
            }

            .custom-card table th,
            .custom-card table td {
                border: 1px solid #ccc;
                color: #000;
            }

            .custom-card table thead,
            .custom-card table tfoot {
                background-color: #eee !important;
                color: #000 !important;
            }

            .custom-card table tbody tr:nth-child(odd) {
                background-color: #f9f9f9 !important;
            }

            .report-title-bar h2,
            .report-title-bar h4,
            .right-info {
                color: #000 !important;
            }

            .print-btn {
                display: none !important;
            }

            body * {
                visibility: hidden;
            }

            .custom-card,
            .custom-card * {
                visibility: visible;
            }
        }
    </style>

    <div class="container mt-4">
        <div class="card shadow border-0 rounded-3 p-4 custom-card">
            <div class="report-title-bar">
                @php $companyName = \App\Models\Setting::value('CompanyName'); @endphp
                <h2 class="company-name">{{ $companyName ?? 'Default Company' }}</h2>

                <h4 class="fw-bold text-white">
                    @if(isset($supplierCode) && $supplierCode === 'L')
                        (සිල්ලර) මුළු අයිතම විකිණුම් – ප්‍රමාණ අනුව
                    @elseif(isset($supplierCode) && $supplierCode === 'A')
                        (තොග) මුළු අයිතම විකිණුම් – ප්‍රමාණ අනුව
                    @else
                        මුළු අයිතම විකිණුම් – ප්‍රමාණ අනුව
                    @endif
                </h4>
                <span class="right-info">{{ now()->format('Y-m-d') }}</span>
                <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
            </div>

            {{-- GRN Info --}}
            @if ($selectedGrnEntry)
                <div class="mb-3 text-white">
                    <strong>GRN කේතය:</strong> {{ $selectedGrnCode }}
                    @if ($selectedGrnEntry->supplier ?? false)
                        , <strong>Supplier:</strong> {{ $selectedGrnEntry->supplier }}
                    @endif
                </div>
            @endif

            {{-- Date Range --}}
            @if (!empty($startDate) || !empty($endDate))
                <div class="mb-3 text-white">
                    <strong>දින පරාසය:</strong>
                    {{ $startDate ? $startDate : '' }}
                    {{ $endDate ? ' සිට ' . $endDate . ' දක්වා' : '' }}
                </div>
            @endif

            <table class="table table-sm table-bordered table-striped compact-table text-center align-middle">
                <thead>
                    <tr>
                        <th>අයිතම කේතය</th>
                        <th>වර්ගය</th>
                        <th>බර (kg)</th>
                        <th>මලු</th>
                        <th>මලු ගාස්තුව (Rs)</th>
                        <th>එකතුව (Rs)</th>
                    </tr>
                </thead>

                <tbody>
                    @php
                        $total_packs = 0;
                        $total_weight = 0;
                        $total_pack_due_cost = 0;
                        $total_net_total = 0;
                    @endphp

                    @forelse ($sales as $sale)
                        @php
                            $pack_due = $sale->pack_due ?? 0;
                            $packs = $sale->packs ?? 0;
                            $weight = $sale->weight ?? 0;
                            $price_per_kg = $sale->price_per_kg ?? 0;
                            $item_total = $sale->total ?? 0;

                            $pack_due_cost = $packs * $pack_due;
                            $net_total = $item_total - $pack_due_cost;

                            $total_packs += $packs;
                            $total_weight += $weight;
                            $total_pack_due_cost += $pack_due_cost;
                            $total_net_total += $net_total;
                        @endphp
                        <tr>
                            <td>{{ $sale->item_code }}</td>
                            <td class="text-start">{{ $sale->item_name }}</td>
                            <td class="text-end">{{ number_format($weight, 2) }}</td>
                            <td class="text-end">{{ number_format($packs, 0) }}</td>
                            <td class="text-end">{{ number_format($pack_due_cost, 2) }}</td>
                            <td class="text-end">{{ number_format($net_total, 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center text-white bg-secondary">වාර්තා නැත</td>
                        </tr>
                    @endforelse
                </tbody>

                <tfoot>
                    <!-- Subtotals -->
                    <tr class="table-secondary fw-bold">
                        <td colspan="2" class="text-end">මුළු එකතුව:</td>
                        <td class="text-end">{{ number_format($total_weight, 2) }}</td>
                        <td class="text-end">{{ number_format($total_packs, 0) }}</td>
                        <td class="text-end">{{ number_format($total_pack_due_cost, 2) }}</td>
                        <td class="text-end">{{ number_format($total_net_total, 2) }}</td>
                    </tr>

                    <!-- Separator -->
                    <tr>
                        <td colspan="6" class="p-0">
                            <hr class="m-0">
                        </td>
                    </tr>

                    <!-- Final Total -->
                    <tr class="table-dark fw-bold">
                        <td colspan="4"></td>
                        <td class="text-end">අවසන් මුළු එකතුව:</td>
                        @php
                            $final_total = $total_net_total + $total_pack_due_cost;
                        @endphp
                        <td class="text-end">{{ number_format($final_total, 2) }}</td>
                    </tr>
                </tfoot>

            </table>
        </div>

        {{-- Export Buttons --}}
        <div class="mt-3">
            <form action="{{ route('report.download', ['reportType' => 'grn-sales-report', 'format' => 'excel']) }}"
                method="POST" class="d-inline">
                @csrf
                @foreach ($filters as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-success me-2">Download Excel</button>
            </form>

            <form action="{{ route('report.download', ['reportType' => 'grn-sales-report', 'format' => 'pdf']) }}"
                method="POST" class="d-inline">
                @csrf
                @foreach ($filters as $key => $value)
                    <input type="hidden" name="{{ $key }}" value="{{ $value }}">
                @endforeach
                <button type="submit" class="btn btn-danger">Download PDF</button>
            </form>
        </div>
    </div>
@endsection