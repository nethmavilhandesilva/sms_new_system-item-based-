@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99 !important;
        font-family: Arial, sans-serif;
    }
    .report-container {
        background-color: #004d00;
        padding: 20px;
        border-radius: 10px;
        color: white;
    }
    .report-title {
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        margin-bottom: 20px;
    }
    .print-btn, .filter-btn, .clear-btn {
        background-color: #28a745;
        border: none;
        padding: 6px 12px;
        border-radius: 5px;
        color: #fff;
        cursor: pointer;
        margin-left: 5px;
    }
    .print-btn:hover, .filter-btn:hover, .clear-btn:hover {
        background-color: #218838;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        color: white;
    }
    table th, table td {
        border: 1px solid #ffffff;
        padding: 8px;
        text-align: left;
    }
    table thead {
        background-color: #003300;
    }
    table tfoot {
        font-weight: bold;
    }

    /* ---------- PRINT STYLES ---------- */
    @media print {
        /* Hide everything except the report container */
        body * {
            visibility: hidden;
        }
        .report-container, .report-container * {
            visibility: visible;
        }

        /* Print layout */
        .report-container {
            position: absolute;
            left: 0;
            top: 0;
            width: 100%;
            background: white !important;
            color: black !important;
            padding: 20px;
            margin: 0;
        }

        /* Hide navigation/sidebar */
        .navbar, .sidebar, header, footer {
            display: none !important;
        }

        /* Black text only */
        table, th, td, h2, p, label, strong, span {
            color: black !important;
            border-color: black !important;
        }

        /* Hide buttons and filter form */
        .print-btn, .filter-btn, .clear-btn, form {
            display: none !important;
        }

        /* Prevent breaking title and table apart */
        .report-container {
            page-break-inside: avoid !important;
        }
        .report-title, table {
            page-break-inside: avoid !important;
        }

        /* Adjust font size and alignment */
        table td, table th {
            font-size: 14px;
            padding: 6px;
        }

        /* Force everything to print on one page if possible */
        html, body {
            height: auto;
            zoom: 90%;
        }

        /* Add a bit of margin for page edges */
        @page {
            margin: 10mm;
        }
    }
</style>

<div class="container mt-4 report-container">
    <div class="report-title">
        <div>
            <h2>📄 Income & Expenses Report</h2>

            <form action="{{ route('income.expenses.report') }}" method="GET" style="display: flex; gap: 10px; align-items: center; margin-top:5px;">
                <label>
                    Start Date: <input type="date" name="start_date" value="{{ $startDate }}">
                </label>
                <label>
                    End Date: <input type="date" name="end_date" value="{{ $endDate }}">
                </label>
                <button type="submit" class="filter-btn">Filter</button>
                <a href="{{ route('income.expenses.report') }}" class="clear-btn">Clear</a>
            </form>

            <p>Showing records from <strong>{{ \Carbon\Carbon::parse($startDate)->format('Y-m-d') }}</strong> 
               to <strong>{{ \Carbon\Carbon::parse($endDate)->format('Y-m-d') }}</strong></p>
        </div>

        <button class="print-btn" onclick="window.print()">🖨️ Print</button>
    </div>

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th>ලැබීම්</th>
                <th>ගැනීම</th>
            </tr>
        </thead>
        <tbody>
            @foreach($reportData as $row)
                <tr>
                    <td>{{ $row['description'] }}</td>
                    <td>{{ $row['dr'] ? number_format(abs($row['dr']), 2) : '' }}</td>
                    <td>{{ $row['cr'] ? number_format(abs($row['cr']), 2) : '' }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="background-color: #006600;">
                <td>Total</td>
                <td>{{ number_format(abs($totalDr), 2) }}</td>
                <td>{{ number_format(abs($totalCr), 2) }}</td>
            </tr>
            <tr style="background-color: #004d00;">
                <td>Net Amount</td>
                <td colspan="2">
                    @php $diff = $totalDr - $totalCr; @endphp
                    @if($diff < 0)
                        <span class="text-danger">{{ number_format($diff, 2) }}</span>
                    @else
                        <span class="text-success">{{ number_format($diff, 2) }}</span>
                    @endif
                </td>
            </tr>
        </tfoot>
    </table>
</div>
@endsection

