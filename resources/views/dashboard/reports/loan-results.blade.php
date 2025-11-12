@extends('layouts.app')

@section('content')

<style>
    body {
        background-color: #99ff99;
    }

    /* ===== PRINT SETTINGS ===== */
    @media print {
        body * {
            visibility: hidden;
        }

        .custom-card,
        .custom-card * {
            visibility: visible;
        }

        .custom-card {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
        }

        body,
        .custom-card {
            background-color: white !important;
            color: black !important;
        }
    }

    .custom-card {
        background-color: #006400 !important;
        color: white;
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

    .custom-card table tbody tr:nth-child(odd) {
        background-color: #00550088;
    }

    .custom-card table tbody tr:nth-child(even) {
        background-color: transparent;
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
</style>

<div class="container mt-4" style="background-color: #99ff99; min-height: 100vh; padding: 20px;">
    <div class="card custom-card shadow border-0 rounded-3 p-4">
        <div class="report-title-bar">
            @php
                $companyName = \App\Models\Setting::value('CompanyName');
                $settingDate = \App\Models\Setting::value('value');
            @endphp

            <h2 class="company-name">{{ $companyName ?? 'Default Company' }}</h2>
            <h4 class="fw-bold text-white">ණය වාර්තාව</h4>

            <span class="right-info">
                {{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d') }}
            </span>

            <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
        </div>

        <div class="card-body p-0">
            @if ($errors->any())
                <div class="alert alert-danger m-3">
                    <ul class="mb-0">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @if ($loans->isEmpty())
                <div class="alert alert-info m-3">
                    No loan records found for the selected filters.
                </div>
            @else
                <table class="table table-bordered table-striped table-hover mb-0">
                    <thead>
                        <tr>
                            <th>දිනය</th>
                            <th>පාරිභෝගික නම</th>
                            <th>බිල් අංකය</th>
                            <th>විස්තරය</th>
                            <th>චෙක්පත්</th>
                            <th>බැංකුව</th>
                            <th>ලබීම්</th>
                            <th>ගැනීම</th>
                            <th>ශේෂය</th>
                        </tr>
                    </thead>
                    <tbody>
                        @php
                            $receivedTotal = 0;
                            $paidTotal = 0;
                            $runningBalance = 0; // 🆕 Running cumulative balance
                        @endphp

                        @foreach ($loans as $loan)
                            @php
                                if ($loan->loan_type === 'old') {
                                    $receivedTotal += $loan->amount;
                                    $receivedAmount = $loan->amount;
                                    $paidAmount = 0;
                                } elseif ($loan->loan_type === 'today') {
                                    $paidTotal += $loan->amount;
                                    $receivedAmount = 0;
                                    $paidAmount = $loan->amount;
                                } else {
                                    $receivedAmount = 0;
                                    $paidAmount = 0;
                                }

                                // 🧮 Update cumulative balance
                                $runningBalance += ($paidAmount - $receivedAmount);
                            @endphp

                            <tr>
                                <td>{{ $loan->created_at ? $loan->created_at->format('Y-m-d') : 'N/A' }}</td>
                                <td>{{ $loan->customer_short_name }}</td>
                                <td>{{ $loan->bill_no }}</td>
                                <td>{{ $loan->description }}</td>
                                <td>{{ $loan->cheque_no }}</td>
                                <td>{{ $loan->bank }}</td>
                                <td>{{ $receivedAmount ? number_format($receivedAmount, 2) : '' }}</td>
                                <td>{{ $paidAmount ? number_format($paidAmount, 2) : '' }}</td>
                                <td>{{ number_format($runningBalance, 2) }}</td>
                            </tr>
                        @endforeach

                        <!-- Totals -->
                        <tr style="font-weight: bold; background-color: #dff0d8; color: black;">
                            <td colspan="6" class="text-end">එකතුව:</td>
                            <td>{{ number_format($receivedTotal, 2) }}</td>
                            <td>{{ number_format($paidTotal, 2) }}</td>
                            <td>{{ number_format($paidTotal - $receivedTotal, 2) }}</td>
                        </tr>

                        <!-- Net Balance -->
                        <tr style="font-weight: bold; background-color: #004d00; color: white;">
                            <td colspan="8" class="text-end">ශුද්ධ ශේෂය:</td>
                            <td>{{ number_format($paidTotal - $receivedTotal, 2) }}</td>
                        </tr>
                    </tbody>
                </table>
            @endif
        </div>
    </div>
</div>
@endsection
