@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99 !important;
        font-family: "Segoe UI", sans-serif;
    }

    .page-container {
        background-color: white;
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 0 15px rgba(0,0,0,0.1);
    }

    .card {
        border: none;
    }

    .table thead th {
        background-color: #006400;
        color: white;
    }

    .table tbody tr:nth-child(odd) {
        background-color: #f8fff8;
    }

    .table tbody tr:hover {
        background-color: #e0ffe0;
        transition: 0.2s;
    }

    .positive-amount {
        color: #28a745;
        font-weight: 600;
    }

    .negative-amount {
        color: #dc3545;
        font-weight: 600;
    }

    .search-box input {
        border-radius: 20px;
        border: 1px solid #ccc;
        padding: 6px 12px;
        outline: none;
        transition: 0.2s;
        text-transform: uppercase;
    }

    .search-box input:focus {
        border-color: #006400;
        box-shadow: 0 0 5px #00640050;
    }

    .order-select {
        border-radius: 20px;
    }

    .badge {
        font-size: 0.85rem;
    }
</style>

<div class="container-fluid py-4">
    <div class="page-container">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h4 class="fw-bold text-success mb-2">
                <i class="fas fa-file-alt me-2"></i>Loan Details Report
            </h4>

            <div class="d-flex gap-3 flex-wrap">
                <div class="search-box">
                   <input type="text" id="searchInput" placeholder="🔍 Search by Short Name...">
                </div>

                <select id="orderBySelect" class="form-select order-select shadow-sm" onchange="sortReport()">
                    <option value="default" selected>Default</option>
                    <option value="amount">By Amount</option>
                    <option value="days">By Settling Days</option>
                </select>
            </div>
        </div>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="loanTable">
                <thead>
                    <tr>
                        <th>Short Name</th>
                        <th>Name</th>
                        <th>Telephone</th>
                        <th>Amount Diff</th>
                        <th>Last Loan Taken</th>
                        <th>Last Loan Settled</th>
                        <th>Days Not Settled</th>
                    </tr>
                </thead>
                <tbody id="loanTableBody">
                    @forelse($report as $index => $row)
                    @php
                        $lastLoanTaken = !empty($row['last_loan_taken']) ? \Carbon\Carbon::parse($row['last_loan_taken']) : null;
                        $lastLoanSettled = !empty($row['last_loan_settled']) ? \Carbon\Carbon::parse($row['last_loan_settled']) : null;

                        if ($lastLoanTaken) {
                            // If settled is missing or before taken, calculate from today
                            if (!$lastLoanSettled || $lastLoanSettled->lt($lastLoanTaken)) {
                                $daysNotSettled = $lastLoanTaken->diffInDays(\Carbon\Carbon::now());
                            } else {
                                $daysNotSettled = $lastLoanTaken->diffInDays($lastLoanSettled);
                            }
                        } else {
                            $daysNotSettled = $row['days_not_settled'] ?? 0;
                        }

                        $daysNotSettled = abs(intval(round($daysNotSettled)));
                    @endphp
                    <tr data-index="{{ $index }}">
                        <td>{{ $row['customer_short_name'] }}</td>
                        <td>{{ $row['customer_name'] }}</td>
                        <td>{{ $row['customer_telephone'] }}</td>
                        <td data-amount="{{ $row['amount_difference'] }}" class="{{ $row['amount_difference'] >= 0 ? 'positive-amount' : 'negative-amount' }}">
                            Rs. {{ number_format($row['amount_difference'], 2) }}
                        </td>
                        <td>
                            @if($lastLoanTaken)
                                {{ $lastLoanTaken->format('M d, Y') }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            @if($lastLoanSettled)
                                {{ $lastLoanSettled->format('M d, Y') }}
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td data-days="{{ $daysNotSettled }}">
                            <span class="badge {{ $daysNotSettled > 30 ? 'bg-danger' : ($daysNotSettled > 15 ? 'bg-warning' : 'bg-success') }}">
                                {{ $daysNotSettled }} days
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="7" class="text-center text-muted">No data available.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Footer -->
        <div class="text-center text-muted small mt-3">
            <i class="fas fa-clock me-1"></i> Last updated: {{ now()->format('M d, Y h:i A') }}
        </div>
    </div>
</div>

<script>
    const tbody = document.getElementById("loanTableBody");
    const originalRows = Array.from(tbody.querySelectorAll("tr"));

    // Dropdown sorting
    function sortReport() {
        const orderBy = document.getElementById("orderBySelect").value;
        const rows = Array.from(originalRows);

        if (orderBy === "default") {
            tbody.innerHTML = "";
            originalRows.forEach(r => tbody.appendChild(r));
            return;
        }

        const key = orderBy === "amount" ? "data-amount" : "data-days";
        rows.sort((a, b) => parseFloat(b.querySelector(`[${key}]`).dataset[orderBy]) - parseFloat(a.querySelector(`[${key}]`).dataset[orderBy]));

        tbody.innerHTML = "";
        rows.forEach(r => tbody.appendChild(r));
    }

    // Prefix search on Short Name only
    document.getElementById("searchInput").addEventListener("keyup", function() {
        const value = this.value.toLowerCase();
        const rows = tbody.querySelectorAll("tr");

        rows.forEach(row => {
            const shortName = row.children[0].innerText.toLowerCase();
            if (shortName.startsWith(value)) {
                row.style.display = "";
            } else {
                row.style.display = "none";
            }
        });
    });
</script>
@endsection
