@extends('layouts.app')

@section('content')
    <style>
        body {
            background: linear-gradient(135deg, #99ff99, #99ff99);
        }

        .report-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin: 30px auto;
            max-width: 1200px;
        }

        .report-title {
            text-align: center;
            font-size: 26px;
            color: #006400;
            font-weight: bold;
            margin-bottom: 15px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            background: white;
        }

        th,
        td {
            border: 1px solid #ccc;
            padding: 10px;
            text-align: center;
            font-size: 14px;
        }

        th {
            background: #228B22;
            color: white;
        }

        .sub-table {
            margin-top: 10px;
            border: 1px solid #ddd;
        }

        .sub-table th {
            background: #e8f5e9;
            color: #333;
        }

        .print-btn {
            display: block;
            margin: 10px auto 20px auto;
            padding: 10px 20px;
            background-color: #228B22;
            color: white;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            font-size: 16px;
        }

        .print-btn:hover {
            background-color: #1b6e1b;
        }

        /* Filter form styling */
        form.bg-light {
            background: #e8f5e9 !important;
            border: 1px solid #99ff99;
        }

        .clickable-row {
            cursor: pointer;
            transition: background-color 0.15s;
        }

        .clickable-row:hover {
            background-color: #f1f8e9;
        }

        .modal-header {
            background-color: #228B22;
            color: white;
            border-bottom: none;
        }

        .modal-title {
            font-weight: bold;
        }

        .modal-table th {
            background: #e8f5e9;
            color: #333;
            font-size: 13px;
        }

        /* Print styles */
        @media print {
            body {
                background: white !important;
            }

            body * {
                visibility: hidden;
            }

            .report-container,
            .report-container * {
                visibility: visible;
            }

            .report-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }

            .report-container,
            .report-container table,
            .report-container th,
            .report-container td {
                color: black !important;
                font-weight: bold !important;
            }

            .print-btn {
                display: none;
            }
        }
    </style>

    {{-- ✅ FILTER SECTION --}}
    <div class="container mt-4">
        <form method="GET" action="{{ route('grn.report2') }}" class="row g-3 p-3 bg-light rounded shadow-sm">

            {{-- Supplier Code Filter --}}
            <div class="col-md-3">
                <label for="supplier_code" class="form-label fw-bold text-success">Supplier Code</label>
                <select name="supplier_code" id="supplier_code" class="form-select">
                    <option value="">All</option>
                    @foreach ($supplierCodes as $code)
                        <option value="{{ $code }}" {{ request('supplier_code') == $code ? 'selected' : '' }}>
                            {{ $code }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Item Code Filter --}}
            <div class="col-md-3">
                <label for="item_code" class="form-label fw-bold text-success">Item Code</label>
                <select name="item_code" id="item_code" class="form-select">
                    <option value="">All</option>
                    @foreach ($itemCodes as $item)
                        <option value="{{ $item->item_code }}" {{ request('item_code') == $item->item_code ? 'selected' : '' }}>
                            {{ $item->item_code }} - {{ $item->item_name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- Date Range Filters --}}
            <div class="col-md-3">
                <label for="start_date" class="form-label fw-bold text-success">Start Date</label>
                <input type="date" id="start_date" name="start_date" value="{{ request('start_date') }}"
                    class="form-control">
            </div>

            <div class="col-md-3">
                <label for="end_date" class="form-label fw-bold text-success">End Date</label>
                <input type="date" id="end_date" name="end_date" value="{{ request('end_date') }}" class="form-control">
            </div>

            <div class="col-12 text-center mt-2">
                <button type="submit" class="btn btn-success px-4">Apply Filters</button>
                <a href="{{ route('grn.report2') }}" class="btn btn-secondary px-4">Reset</a>
            </div>
        </form>
    </div>

    {{-- ✅ REPORT SECTION --}}
    <div class="report-container">
        <button class="print-btn" onclick="window.print()">🖨️ Print Report</button>
        <h2 class="report-title">Goods Received Note (GRN) Report</h2>

        {{-- 🔍 SEARCH BAR --}}
        <div class="d-flex justify-content-end mb-3">
            <input type="text" id="search-grn-code" class="form-control w-25 border-success text-uppercase"
                placeholder="🔍 Search by GRN Code..." style="text-transform: uppercase;">
        </div>

        <table>
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Supplier Code</th>
                    <th>Item Code</th>
                    <th>Item Name</th>
                    <th>Packs</th>
                    <th>Weight</th>
                    <th>Txn Date</th>
                    <th>Original Packs</th>
                    <th>Original Weight</th>
                    <th>GRN No</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($grnEntries as $entry)
                    <tr class="clickable-row" data-bs-toggle="modal" data-bs-target="#grnDetailsModal"
                        data-grn-code="{{ $entry->code }}">
                        <td>{{ $entry->code }}</td>
                        <td>{{ $entry->supplier_code }}</td>
                        <td>{{ $entry->item_code }}</td>
                        <td>{{ $entry->item_name }}</td>
                        <td>{{ $entry->packs }}</td>
                        <td>{{ $entry->weight }}</td>
                        <td>{{ $entry->txn_date }}</td>
                        <td>{{ $entry->original_packs }}</td>
                        <td>{{ $entry->original_weight }}</td>
                        <td>{{ $entry->grn_no }}</td>
                    </tr>

                    @if (isset($grnEntry2Data[$entry->code]))
                        <tr>
                            <td colspan="10">
                                <table class="sub-table">
                                    <thead>
                                        <tr>
                                            <th>Item Code</th>
                                            <th>Item Name</th>
                                            <th>Packs</th>
                                            <th>Weight</th>
                                            <th>Per KG Price</th>
                                            <th>Type</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($grnEntry2Data[$entry->code] as $sub)
                                            <tr>
                                                <td>{{ $sub->item_code }}</td>
                                                <td>{{ $sub->item_name }}</td>
                                                <td>{{ $sub->packs }}</td>
                                                <td>{{ $sub->weight }}</td>
                                                <td>{{ $sub->per_kg_price }}</td>
                                                <td>{{ $sub->type }}</td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr>
                        <td colspan="10" class="text-center text-muted">No records found. Please apply filters.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- ✅ GRN Details Modal --}}
    <div class="modal fade" id="grnDetailsModal" tabindex="-1" aria-labelledby="grnDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="grnDetailsModalLabel">GRN Details for Code: <span
                            id="modal-grn-code"></span></h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body">

                    <h4>Related Sale Records</h4>
                    <table class="table table-bordered modal-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Customer Code</th>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Packs</th>
                                <th>Weight</th>
                                <th>Price/KG</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="sale-details-body">
                            <tr>
                                <td colspan="8" class="text-center text-muted">Loading...</td>
                            </tr>
                        </tbody>
                    </table>
                    <hr>
                    <h4>Related Entry Details</h4>
                    <table class="table table-bordered mb-4 modal-table">
                        <thead>
                            <tr>
                                <th>Item Code</th>
                                <th>Item Name</th>
                                <th>Packs</th>
                                <th>Weight</th>
                                <th>Per KG Price</th>
                                <th>Type</th>
                            </tr>
                        </thead>
                        <tbody id="grn-entry2-details-body">
                            <tr>
                                <td colspan="6" class="text-center text-muted">Loading...</td>
                            </tr>
                        </tbody>
                    </table>

                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <script>
        $(document).ready(function () {

            // 🔍 Live search filter for GRN Code
            $('#search-grn-code').on('keyup', function () {
                var searchValue = $(this).val().toLowerCase();
                $('table tbody tr.clickable-row').filter(function () {
                    var code = $(this).find('td:first').text().toLowerCase();
                    $(this).toggle(code.startsWith(searchValue));
                });

                // Keep related sub-rows synced
                $('table tbody tr').not('.clickable-row').each(function () {
                    var prevRow = $(this).prev('.clickable-row');
                    $(this).toggle(prevRow.is(':visible'));
                });
            });

            // 🟢 Clickable rows open modal + fetch details
            $('.clickable-row').on('click', function () {
                var grnCode = $(this).data('grn-code');
                $('#modal-grn-code').text(grnCode);

                $('#grn-entry2-details-body').html('<tr><td colspan="6" class="text-center text-muted">Loading related GRN entries...</td></tr>');
                $('#sale-details-body').html('<tr><td colspan="8" class="text-center text-muted">Loading related Sale records...</td></tr>');

                $.ajax({
                    url: '{{ route('grn.fetch.details') }}',
                    method: 'GET',
                    data: { code: grnCode },
                    success: function (response) {
                        var grnEntry2Html = '';
                        if (response.grnEntry2.length > 0) {
                            $.each(response.grnEntry2, function (i, item) {
                                grnEntry2Html += '<tr>' +
                                    '<td>' + item.item_code + '</td>' +
                                    '<td>' + item.item_name + '</td>' +
                                    '<td>' + item.packs + '</td>' +
                                    '<td>' + item.weight + '</td>' +
                                    '<td>' + item.per_kg_price + '</td>' +
                                    '<td>' + item.type + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            grnEntry2Html = '<tr><td colspan="6" class="text-center text-success">No related GrnEntry2 records found.</td></tr>';
                        }
                        $('#grn-entry2-details-body').html(grnEntry2Html);

                        var saleHtml = '';
                        if (response.sales.length > 0) {
                            $.each(response.sales, function (i, sale) {
                                saleHtml += '<tr>' +
                                    '<td>' + sale.Date + '</td>' +
                                    '<td>' + sale.customer_code + '</td>' +
                                    '<td>' + sale.item_code + '</td>' +
                                    '<td>' + sale.item_name + '</td>' +
                                    '<td>' + sale.packs + '</td>' +
                                    '<td>' + sale.weight + '</td>' +
                                    '<td>' + sale.price_per_kg + '</td>' +
                                    '<td>' + sale.total + '</td>' +
                                    '</tr>';
                            });
                        } else {
                            saleHtml = '<tr><td colspan="8" class="text-center text-success">No related Sale records found.</td></tr>';
                        }
                        $('#sale-details-body').html(saleHtml);
                    },
                    error: function (xhr) {
                        $('#grn-entry2-details-body').html('<tr><td colspan="6" class="text-center text-danger">Error loading GrnEntry2 data.</td></tr>');
                        $('#sale-details-body').html('<tr><td colspan="8" class="text-center text-danger">Error loading Sale data.</td></tr>');
                    }
                });
            });
        });
    </script>
@endsection