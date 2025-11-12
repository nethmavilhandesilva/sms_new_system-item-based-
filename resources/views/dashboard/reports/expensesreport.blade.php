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
        text-align: center;
    }

    .table tbody tr:nth-child(odd) {
        background-color: #f8fff8;
    }

    .table tbody tr:hover {
        background-color: #e0ffe0;
        transition: 0.2s;
    }

    .text-end {
        text-align: right;
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

</style>

<div class="container-fluid py-4">
    <div class="page-container">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
            <h4 class="fw-bold text-success mb-2">
                <i class="fas fa-file-alt me-2"></i>Expenses Report
            </h4>

            <div class="d-flex gap-3 flex-wrap">
                <div class="search-box">
                   <input type="text" id="searchInput" placeholder="🔍 Search Description...">
                </div>
            </div>
        </div>

        <!-- Filters -->
        <form method="GET" class="row g-3 mb-4">
            <div class="col-md-3">
                <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
            </div>
            <div class="col-md-3">
                <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100">Filter</button>
            </div>
        </form>

        <!-- Table -->
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="expensesTable">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($expenses as $expense)
                        <tr>
                            <td class="text-center">{{ $expense->Date }}</td>
                            <td>{{ $expense->description }}</td>
                            <td class="text-end">{{ number_format(abs($expense->amount), 2) }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center">No records found.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot>
                    <tr>
                        <th colspan="2" class="text-end">Total</th>
                        <th class="text-end">{{ number_format(abs($expenses->sum('amount')), 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<script>
    // Search Description only
    const searchInput = document.getElementById('searchInput');
    const tableBody = document.querySelector('#expensesTable tbody');
    searchInput.addEventListener('keyup', function() {
        const value = this.value.toLowerCase();
        tableBody.querySelectorAll('tr').forEach(row => {
            const desc = row.children[1].innerText.toLowerCase();
            row.style.display = desc.includes(value) ? '' : 'none';
        });
    });
</script>

@endsection
