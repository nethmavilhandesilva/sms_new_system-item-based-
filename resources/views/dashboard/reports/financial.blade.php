@extends('layouts.app')

@section('content')
    <style>
        body {
            background-color: #99ff99 !important;
            font-family: Arial, sans-serif;
        }

        .report-card {
            background-color: #004d00;
            color: #ffffff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.2);
        }

        .report-title-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .company-name {
            margin: 0;
            font-size: 28px;
        }

        .print-btn {
            background-color: #28a745;
            border: none;
            padding: 8px 15px;
            border-radius: 5px;
            color: #fff;
            cursor: pointer;
        }

        .print-btn:hover {
            background-color: #218838;
        }

        .right-info {
            font-size: 14px;
            color: #ffffff;
        }

        table {
            background-color: #004d00;
            color: #ffffff;
        }

        thead th {
            background-color: #003300;
        }

        tfoot tr.table-warning td {
            background-color: #006600;
            font-weight: bold;
        }

        .alert-info {
            background-color: #006600;
            color: #ffffff;
            border: none;
            font-weight: bold;
        }

        /* 🖨️ Hide sidebar and buttons when printing */
        @media print {

            .sidebar,
            .navbar,
            .print-btn,
            .alert-info,
            a.print-btn {
                display: none !important;
            }

            body {
                background: white !important;
                color: black !important;
            }

            .report-card {
                box-shadow: none;
                color: black !important;
                background-color: white !important;
            }

            table {
                color: black !important;
                background-color: white !important;
            }

            thead th {
                background-color: #ddd !important;
                color: black !important;
            }

            tfoot tr.table-warning td {
                background-color: #f0f0f0 !important;
                color: black !important;
            }
        }
    </style>

    <div class="container mt-4 report-card">
        <div class="report-title-bar">
            <div>
                @php
                    $companyName = \App\Models\Setting::value('CompanyName');
                @endphp
                <h2 class="company-name">{{ $companyName ?? 'Default Company' }}</h2>
                <h4 class="fw-bold">📄 විකුණුම් වාර්තාව</h4>
            </div>
            <div>
                @php
                    $settingDate = \App\Models\Setting::value('value');
                @endphp

                <span class="right-info">
                    {{ \Carbon\Carbon::parse($settingDate)->format('Y-m-d H:i') }}
                </span>
                <button class="print-btn" onclick="window.print()">🖨️ මුද්‍රණය</button>
            </div>
        </div>

        {{-- Show Sales Total at the top --}}
        <div class="alert alert-info fw-bold">
            Sales Total: {{ number_format($salesTotal, 2) }}
        </div>

        <table class="table table-bordered table-striped">
            <thead>
                <tr>
                    <th>විස්තරය</th>
                    <th>ලැබීම්</th>
                    <th>ගෙවීම</th>
                </tr>
            </thead>
            <tbody>
                {{-- Loop through all report data, including Balance row from controller --}}
                @foreach($reportData as $row)
                    <tr>
                        <td>{{ $row['description'] }}</td>
                        <td>{{ $row['dr'] ? number_format(abs($row['dr']), 2) : '' }}</td>
                        <td>{{ $row['cr'] ? number_format(abs($row['cr']), 2) : '' }}</td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                <tr class="fw-bold">
                    <td>Total</td>
                    <td>{{ number_format(abs($totalDr), 2) }}</td>
                    <td>{{ number_format(abs($totalCr), 2) }}</td>
                </tr>
                <tr class="fw-bold table-warning">
                    <td>ඇතැති මුදල්</td>
                    <td colspan="2">
                        @php
                            $diff = $totalDr + $totalCr;
                        @endphp
                        @if($diff < 0)
                            <span class="text-danger">{{ number_format($diff, 2) }}</span>
                        @else
                            <span class="text-success">{{ number_format($diff, 2) }}</span>
                        @endif
                    </td>
                </tr>
                <tr class="fw-bold table-warning" id="profit-row">
                    <td>💰 Profit</td>
                    <td colspan="2" class="text-success">
                        <span id="profit-value" style="display: none;">
                            {{ number_format($totalProfit, 2) }}
                            <br>
                           
                        </span>
                        <input type="password" id="profit-password" placeholder="Enter password" style="padding:2px 5px;" />
                    </td>
                </tr>
                <tr class="fw-bold table-warning">
                    <td>Total Damages</td>
                    <td colspan="2" class="text-danger">
                        {{ number_format($totalDamages, 2) }}
                    </td>
                </tr>
                <tr class="fw-bold table-warning">
                    <td colspan="3">
                        <div style="display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                            <div
                                style="flex:1; background-color:#004d00; padding:10px; border-radius:5px; text-align:center; color:white;">
                                අද දින පරණ නය <br>
                                <strong>{{ $totalOldLoans }}</strong>
                            </div>
                            <div
                                style="flex:1; background-color:#006600; padding:10px; border-radius:5px; text-align:center; color:white;">
                               අද දින නය ගැනීම<br>
                               <strong>{{ abs($totaltodayLoans) }}</strong>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="fw-bold table-warning">
                    <td colspan="3">
                        <div style="display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                            <div
                                style="flex:1; background-color:#004d00; padding:10px; border-radius:5px; text-align:center; color:white;">
                                Total Qty Sold<br>
                                <strong>{{ number_format($totalQtySold, 2) }}</strong>
                            </div>
                            <div
                                style="flex:1; background-color:#006600; padding:10px; border-radius:5px; text-align:center; color:white;">
                                Total Bills Printed<br>
                                <strong>{{ $totalBillsPrinted }}</strong>
                            </div>
                        </div>
                    </td>
                </tr>
                <tr class="fw-bold table-warning">
                    <td colspan="3">
                        <div style="display: flex; justify-content: space-between; gap: 10px; flex-wrap: wrap;">
                            <div
                                style="flex:1; background-color:#003300; padding:10px; border-radius:5px; text-align:center; color:white;">
                                First Bill Printed Time<br>
                                <strong>{{ $firstBillTime }}</strong><br>
                                <small>(Bill No: {{ $firstBillNo }})</small>
                            </div>
                            <div
                                style="flex:1; background-color:#005500; padding:10px; border-radius:5px; text-align:center; color:white;">
                                Last Bill Printed Time<br>
                                <strong>{{ $lastBillTime }}</strong><br>
                                <small>(Bill No: {{ $lastBillNo }})</small>
                            </div>
                        </div>
                    </td>
                </tr>
            </tfoot>
        </table>

        <a href="{{ route('send.financial.report') }}" class="print-btn" style="text-decoration: none;">
            📧 Send Email
        </a>
    </div>

    <script>
        const profitInput = document.getElementById("profit-password");
        const profitValue = document.getElementById("profit-value");

        profitInput.addEventListener("input", function () {
            if (this.value === "nethma123") {
                profitValue.style.display = "";
                this.style.display = "none"; // hide input after correct password
            }
        });
    </script>
@endsection