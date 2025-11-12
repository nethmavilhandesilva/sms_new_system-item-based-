@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99;
        font-family: Arial, sans-serif;
    }
    .report-container {
        background-color: #004d00;
        padding: 20px;
        border-radius: 10px;
        color: white;
    }
    table {
        width: 100%;
        border-collapse: collapse;
        margin-top: 15px;
        background: white;
        color: black;
        border-radius: 10px;
        overflow: hidden;
    }
    th, td {
        padding: 10px 15px;
        text-align: left;
        border-bottom: 1px solid #ddd;
    }
    th {
        background-color: #008000;
        color: white;
    }
    tr:hover {
        background-color: #f2f2f2;
    }
    .report-title {
        font-size: 1.8rem;
        text-align: center;
        margin-bottom: 15px;
    }
    .search-box {
        width: 300px;
        margin: 0 auto 15px auto;
        display: block;
        padding: 8px 12px;
        border-radius: 8px;
        border: none;
        outline: none;
        font-size: 1rem;
    }
</style>

<div class="container mt-4">
    <div class="report-container">
        <h2 class="report-title">Supplier Report</h2>

        <!-- ✅ Search Bar -->
      <input type="text" id="supplierSearch" class="search-box" placeholder="Search by Supplier Code..." style="text-transform: uppercase;">

        <table>
            <thead>
                <tr>
                    <th>Supplier Code</th>
                    <th>Name</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <th>Address</th>
                    <th>Total Purchases (Rs.)</th>
                    <th>Total Payments (Rs.)</th>
                    <th>Remaining Balance (Rs.)</th>
                </tr>
            </thead>
            <tbody id="supplierTableBody">
                @foreach($suppliers as $supplier)
                    <tr>
                        <td class="code-col">{{ $supplier['code'] }}</td>
                        <td>{{ $supplier['name'] }}</td>
                        <td>{{ $supplier['phone'] }}</td>
                        <td>{{ $supplier['email'] }}</td>
                        <td>{{ $supplier['address'] }}</td>
                        <td>{{ number_format($supplier['total_purchases'], 2) }}</td>
                        <td>{{ number_format($supplier['total_payments'], 2) }}</td>
                        <td><strong>{{ number_format($supplier['balance'], 2) }}</strong></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

<!-- ✅ Javascript Filtering Logic -->
<script>
    document.getElementById('supplierSearch').addEventListener('keyup', function () {
        let searchVal = this.value.toUpperCase();   // convert to uppercase for matching
        let rows = document.querySelectorAll('#supplierTableBody tr');

        rows.forEach(row => {
            let code = row.querySelector('.code-col').textContent.toUpperCase();

            // Show only if supplier_code starts with the typed letters
            if (code.startsWith(searchVal)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

@endsection
