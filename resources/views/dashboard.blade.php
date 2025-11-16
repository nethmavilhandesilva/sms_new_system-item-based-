@extends('layouts.app')

@section('horizontal_sidebar')
    {{-- This section will contain the content that was originally in the vertical sidebar --}}
    <style>
        .nav-item.dropdown {
            position: relative;
        }

        .nav-item.dropdown .dropdown-menu {
            display: none;
            position: absolute;
            top: 100%;
            left: 0;
            background-color: #1a1a1a;
            padding: 0.25rem 0;
            min-width: 180px;
        }

        .nav-item.dropdown:hover .dropdown-menu {
            display: block;
            /* show on hover */
        }

        .dropdown-menu a {
            color: white;
            display: block;
            padding: 0.25rem 1rem;
            text-decoration: none;
        }

        .dropdown-menu a:hover {
            background-color: #333;
        }
    </style>

    <nav
    class="navbar navbar-expand-lg navbar-light shadow-sm rounded-bottom px-3 py-1 custom-dark-green-bg navbar-compact">
    <div class="container-fluid d-flex justify-content-between align-items-center">
        {{-- Navbar links --}}
        <div class="collapse navbar-collapse" id="navbarNavHorizontal">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                {{-- Dashboard --}}
                <li class="nav-item">
                    <a href="{{ route('dashboard') }}" class="nav-link d-flex align-items-center small">
                        <span class="material-icons me-1 text-primary" style="font-size:1.1em;">dashboard</span>
                        <span class="text-white">Dashboard</span>
                    </a>
                </li>

                {{-- Master Dropdown --}}
                <li class="nav-item dropdown">
                    <a href="#" class="nav-link d-flex align-items-center small text-white">
                        <span class="material-icons me-1">storage</span>
                        Master
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('items.index') }}">භාණ්ඩ</a></li>
                        <li><a class="dropdown-item" href="{{ route('customers.index') }}">ගනුදෙනුකරුවන්</a></li>
                        <li><a class="dropdown-item" href="{{ route('suppliers.index') }}">සැපයුම්කරුවන්</a></li>
                      
                        <li><a class="dropdown-item" href="{{ route('customers-loans.report') }}"> ණය වාර්තාව දැකීම</a>
                        </li>
                        <li>
                            <a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#codeSelectModal">
                                GRN වාර්තාව
                            </a>
                        </li>
                    </ul>
                </li>

                {{-- Income / Expense --}}
                <li class="nav-item">
                    <a href="{{ route('customers-loans.index') }}"
                        class="btn btn-success nav-link d-flex align-items-center small {{ Request::routeIs('customers-loans.index') ? 'active' : '' }}">
                        <span class="material-icons me-1" style="font-size:1.1em;">payments</span>
                        <span class="text-white">ආදායම් / වියදම්</span>
                    </a>
                </li>
                {{-- GRN Button --}}
                <li class="nav-item">
                    <a href="{{ route('grn.create') }}"
                        class="btn btn-success nav-link d-flex align-items-center small {{ Request::routeIs('grn.create') ? 'active' : '' }}">
                        <span class="material-icons me-1" style="font-size:1.1em;">receipt_long</span>
                        <span class="text-white">GRN</span>
                    </a>
                </li>
                 <li class="nav-item">
                    <a href="{{ route('grn.updateform') }}"
                        class="btn btn-success nav-link d-flex align-items-center small {{ Request::routeIs('grn.create') ? 'active' : '' }}">
                        <span class="material-icons me-1" style="font-size:1.1em;">receipt_long</span>
                        <span class="text-white">GRN අලුත් කිරීම</span>
                    </a>
                </li>
            </ul>

            {{-- Day Start Process and Logout on the right --}}
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <a href="#" class="nav-link d-flex align-items-center small" data-bs-toggle="modal"
                        data-bs-target="#dayStartModal">
                        <span class="material-icons me-1 text-blue-600" style="font-size:1.1em;">play_circle_filled</span>
                        <span class="text-white">Day Start Process</span>
                    </a>
                </li>
                <li class="nav-item">
                    <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                        @csrf
                        <button type="submit" class="nav-link d-flex align-items-center small"
                            style="background:none; border:none; padding:0; cursor:pointer;">
                            <span class="material-icons me-1 text-red-600" style="font-size:1.1em;">logout</span>
                            <span style="color: white;">Logout</span>
                        </button>
                    </form>
                </li>
            </ul>
        </div>

        {{-- Next Day Info --}}
        <div class="ms-3 fw-bold text-danger" style="white-space: nowrap;">
            @php
                $lastDay = \App\Models\Setting::where('key', 'last_day_started_date')->first();
                $nextDay = $lastDay ? \Carbon\Carbon::parse($lastDay->value)->format('Y-m-d') : \Carbon\Carbon::now()->format('Y-m-d');
            @endphp
            {{ $nextDay }}
        </div>
    </div>
</nav>


    {{-- NEW: Separate Horizontal Navigation for Reports - FIXED AT BOTTOM --}}
    {{-- NEW: Separate Horizontal Navigation for Reports - FIXED AT BOTTOM --}}
{{-- NEW: Separate Horizontal Navigation for Reports - FIXED AT BOTTOM --}}
<nav class="navbar navbar-expand-lg navbar-dark bg-dark shadow-lg fixed-bottom custom-bottom-nav small">
    <div class="container-fluid">
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNavReports"
            aria-controls="navbarNavReports" aria-expanded="false" aria-label="Toggle report navigation">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-center" id="navbarNavReports">
            <ul class="navbar-nav mb-2 mb-lg-0 d-flex flex-row gap-2">

                <li class="nav-item">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#itemReportModal"
                        class="nav-link text-white px-2 py-1">
                        එළවළු
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#weight_modal"
                        class="nav-link text-white px-2 py-1">
                        බර මත
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#grnSaleReportModal"
                        class="nav-link text-white px-2 py-1">
                        මිල එක්කතුව
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#reportFilterModal9"
                        class="nav-link text-white px-2 py-1">
                        වෙනස් කිරීම
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('report.grn.sales.overview') }}" target="_blank"
                        class="nav-link text-white px-2 py-1">
                        ඉතිරි වාර්තාව 1
                    </a>
                </li>
                <li class="nav-item">
                    <a href="{{ route('report.grn.sales.overview2') }}" target="_blank"
                        class="nav-link text-white px-2 py-1">
                        ඉතිරි වාර්තාව 2
                    </a>
                </li>
                <li class="nav-item">
                    <a href="#" data-bs-toggle="modal" data-bs-target="#filterModal"
                        class="nav-link text-white px-2 py-1">
                        විකුණුම් වාර්තාව
                    </a>
                </li>
            </ul>
        </div>
    </div>
</nav>



    <style>
        /* Custom CSS to push content up if fixed-bottom nav bar covers it */
        body {
            padding-bottom: 70px;
            /* Adjust this value based on the actual height of your fixed-bottom navbar */
        }

        .custom-bottom-nav {
            background-color: #004d00 !important;
            /* A slightly darker green for the bottom nav */
        }

        /* Adjustments for the bottom nav links */
        .custom-bottom-nav .nav-link {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
            font-size: 0.95rem !important;
            /* Slightly larger than the top compact nav */
        }

        .custom-bottom-nav .nav-link .material-icons {
            font-size: 20px !important;
            /* Slightly larger icons */
        }

        /* Center the nav items when collapsed (mobile) and on larger screens */
        .custom-bottom-nav .navbar-collapse {
            justify-content: center;
            /* Centers the ul inside the collapsed div */
        }

        .custom-bottom-nav .navbar-nav {
            width: 100%;
            /* Make ul take full width inside collapse for justify-content to work */
            justify-content: space-around;
            /* Distribute items evenly */
        }

        /* Add horizontal margin between nav items for better spacing on larger screens */
        .custom-bottom-nav .navbar-nav .nav-item {
            margin: 0 5px;
            /* Adjust as needed */
        }
    </style>
    <style>
        /* Adjustments for a more compact navbar */
        .navbar.navbar-compact {
            /* Reduce overall vertical padding of the navbar container */
            padding-top: 0.3rem !important;
            /* Adjust this value */
            padding-bottom: 0.3rem !important;
            /* Adjust this value */
        }

        .navbar.navbar-compact .navbar-nav .nav-link {
            /* Reduce vertical padding within each nav link */
            padding-top: 0.2rem !important;
            /* Adjust this value */
            padding-bottom: 0.2rem !important;
            /* Adjust this value */

            /* Make the text slightly smaller */
            font-size: 0.85rem !important;
            /* Adjust this value, e.g., 0.8rem for even smaller */
        }

        .navbar.navbar-compact .navbar-nav .nav-link .material-icons {
            /* Make the Material Icons smaller */
            font-size: 18px !important;
            /* Default is often 24px, 18px is a good reduction */
            margin-right: 0.3rem !important;
            /* Adjust margin next to icon if needed */
        }
    </style>
@endsection

@section('content')
    {{-- CSS Includes --}}
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />

    {{-- REQUIRED: Minimal inline styling for the new section's appearance and collapse functionality --}}

    <style>
        .printed-sales-list ul,
        .unprinted-sales-list ul {
            list-style: none;
            padding-left: 0;
        }

        .custom-dark-green-bg {
            background-color: #006400 !important;
            /* A common dark green hex code */
        }



        .customer-header {
            background-color: #f8f9fa;
            padding: 10px 15px;
            cursor: pointer;
            font-weight: bold;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .customer-header:hover {
            background-color: #e9ecef;
        }

        .customer-details {
            padding: 10px 15px;
            background-color: #fff;
            border-top: 1px solid #e0e0e0;
            display: none;
            /* Bootstrap's .collapse class handles visibility */
        }

        .customer-details.show {
            /* Bootstrap adds .show when expanded */
            display: block;
        }

        .customer-details table {
            width: 100%;
            margin-top: 10px;
            font-size: 0.85em;
        }

        .customer-details table th,
        .customer-details table td {
            padding: 4px 8px;
            text-align: left;
            border-bottom: 1px dashed #eee;
        }

        .customer-details table th {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .customer-details .sale-item-row:last-child td {
            border-bottom: none;
        }

        .total-for-customer {
            font-weight: bold;
            text-align: right;
            padding: 5px 0;
            border-top: 1px solid #ddd;
            margin-top: 5px;
        }

        .arrow-icon {
            transition: transform 0.3s ease;
        }

        .arrow-icon.rotated {
            transform: rotate(90deg);
        }
    </style>


    <style>
        /* Page background green */
        body,
        html {
            background-color: #99ff99;
            /* Light green background */
        }

        /* Bold black labels */
        label.form-label {
            font-weight: 700;
            color: #000000;
        }

        /* Smaller input fields and selects, with borders */
        input.form-control-sm,
        select.form-select-sm {
            border: 1.5px solid #000000 !important;
            /* stronger black border */
            font-weight: 600;
            font-size: 0.875rem;
            /* smaller font */
        }

        /* Align certain form groups horizontally with smaller width */
        .form-row {
            display: flex;
            flex-wrap: wrap;
            gap: 1rem;
            align-items: flex-end;

        }

        .form-row>div {
            flex: 1 1 150px;
            /* allow shrink/grow, min width 150px */
        }

        /* Adjust card background to white for contrast */
        .card {
            background-color: #111439ff !important;
        }

      /* ---------------------- */
.select2-container--default .select2-selection--single .select2-selection__rendered {
    color: #007bff !important;
    font-weight: bold !important;
    text-align: center !important;
    font-size: 16px !important;
    line-height: 34px !important;
    padding: 0 12px !important;
}

/* Optional: red text for special class */
.select2-black-text {
    color: #FF0000 !important;
}

/* ---------------------- */
/* Tabular option rows */
.grn-option-row {
    display: grid;
    /* Use 'fr' units to fill the available width */
    grid-template-columns: 120px 60px 1fr 60px 60px 60px 70px;
    gap: 1px;
    padding: 2px 4px;
    align-items: center;
    white-space: nowrap;
    font-size: 13px;
    color: #000000 !important;
    background: #fff7cc;
}

/* Columns */
.grn-column { 
    overflow: hidden; 
    text-overflow: ellipsis; 
    white-space: nowrap; 
    padding: 0 2px; 
}
.grn-code { 
    font-weight: bold; 
    text-align: left; 
}
.grn-sp { 
    text-align: center; 
}
.grn-item { 
    /* Change this line */
    text-align: center; 
}
.grn-ow, .grn-op, .grn-bw, .grn-bp { 
    text-align: right; 
}
.grn-txn-date { 
    text-align: center; 
}

/* ---------------------- */
/* Header row styling */
.grn-header-row {
    display: grid;
    /* Use 'fr' units to match the option rows */
    grid-template-columns: 120px 60px 1fr 60px 60px 60px 70px;
    gap: 1px;
    background: #333;
    color: #fff;
    font-weight: bold;
    font-size: 13px;
    padding: 2px 4px;
    border-bottom: 1px solid #ccc;
    white-space: nowrap;
}

/* Highlighted option */
.select2-container--default .select2-results__option--highlighted .grn-option-row {
    background-color: #007bff !important;
    color: #fff !important;
}

/* Remove default padding */
.select2-container--default .select2-results__option {
    padding: 0 !important;
    font-size: 16px !important;
    font-weight: bold !important;
}

    </style>
      <style>
        .col-custom-2-5 {
            flex: 0 0 20.83333333%;
            max-width: 20.83333333%;
        }
        .col-custom-7 {
            flex: 0 0 58.33333333%;
            max-width: 58.33333333%;
        }
        .col-custom-2-5-offset {
            margin-left: 20.83333333%;
        }
    </style>

   <div class="container-fluid" style="margin-top: 10px;">
        <div class="row justify-content-between">
            {{-- Custom Left Column (2.5) --}}
            <div class="col-custom-2-5">
                {{-- ORIGINAL SECTION: Printed Sales Records (bill_printed = 'Y') - Top Left Column --}}
                <div class="card shadow-sm border-0 rounded-3" style="height: 450px;">
                    {{-- Fixed total height --}}
                    <div class="p-3"
                        style="background-color: #004d00; border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                        <h6 class="mb-2 text-center text-white">
                            මුද්‍රිත විකුණුම් වාර්තා
                        </h6>
                        {{-- 🔍 Search Bar --}}
                        <input type="text" id="searchPrintedSales" class="form-control form-control-sm mb-2"
                            placeholder="Search by Bill No or Customer Code...">
                    </div>

                    {{-- Scrollable list area --}}
                    <div style="flex: 1; overflow-y: auto; padding: 0.5rem; background: #5ed772ff;">
                        @if ($salesPrinted->count())
                            <div class="printed-sales-list">
                                <ul id="printedSalesList" style="list-style: none; padding-left: 0; margin: 0;">
                                    {{-- Outer loop: CUSTOMER GROUP --}}
                                    @foreach ($salesPrinted->sortByDesc(fn($sales) => $sales->first()->created_at) as $customerCode => $salesForCustomer)
                                        @php
            $customerName = $salesForCustomer->first()->customer_name ?? 'N/A';
                                        @endphp
                                        <li data-customer-code="{{ $customerCode }}">
                                            <div class="customer-group-header">
                                                {{-- Customer header content here (optional) --}}
                                            </div>
                                            <ul>
                                                {{-- Inner loop: BILL GROUP --}}
                                                @foreach ($salesForCustomer->groupBy('bill_no')->sortByDesc(fn($sales) => $sales->first()->created_at) as $billNo => $salesForBill)
                                                    @php
                $totalBillAmount = $salesForBill->sum('total');
                                                    @endphp
                                                    <li>
                                                        <div class="customer-header bill-clickable"
                                                            data-customer-code="{{ $customerCode }}"
                                                            data-customer-name="{{ $customerName }}" data-bill-no="{{ $billNo ?? '' }}"
                                                            data-bill-type="printed"
                                                            style="font-size: 17px; padding: 2px 6px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ddd; margin-bottom: 3px; border-radius: 4px; background-color: #f9f9f9;">
                                                            <span style="flex: 1;">
                                                                {{ strtoupper($customerCode ?? 'N/A') }} - Rs.
                                                                {{ number_format($totalBillAmount, 2) }}
                                                            </span>
                                                            <i class="material-icons arrow-icon"
                                                                style="font-size: 14px;">keyboard_arrow_right</i>
                                                        </div>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @else
                            <div class="alert alert-info text-center">No printed sales records found.</div>
                        @endif
                    </div>
                </div>

            </div>

            {{-- EXISTING CONTENT: Main Sales Entry and All Sales Table (Custom 7) --}}
            <div class="col-custom-7">
                <div class="card shadow-sm border-0 rounded-3 p-2">
                    @if ($errors->any())
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Whoops!</strong> There were some problems with your input.
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <strong>Success!</strong> {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    @if (session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <strong>Error!</strong> {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('grn.store') }}" id="salesEntryForm">
                        @csrf
                        {{-- NEW TOP ROW: Select Customer Dropdown --}}
                        <div style="display: flex; align-items: center; gap: 6px;">
                            <div id="billNoDisplay"
                                style="color: white; font-weight: bold; font-size: 0.9rem; white-space: nowrap;">
                                {{-- Bill No will be displayed here --}}
                            </div>
                            <h5 style="font-size: 1.5rem; color: red; margin: 0; white-space: nowrap;">
                                <strong>Total Sales Value:</strong> Rs. <span
                                    id="mainTotalSalesValue">{{ number_format($totalSum, 2) }}</span>
                            </h5>
                        </div>

                        <div class="row justify-content-end" style="margin-top: -15px;">
                            <div class="row g-2 align-items-center">
                                {{-- Customer Code Input --}}
                                <div class="col-md-3">
                                    <input type="text" name="customer_code" id="new_customer_code" maxlength="10"
                                        class="form-control text-uppercase @error('customer_code') is-invalid @enderror"
                                        value="{{ old('customer_code') }}" placeholder="පාරිභෝගික කේතය"
                                        style="width: 140px; height: 34px; font-size: 14px; padding: 6px 12px; border: 1px solid black; color: black;"
                                        required>
                                    @error('customer_code')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Customer Select --}}
                                <div class="col-md-6">
                                    <select name="customer_code_select" id="customer_code_select"
                                        class="form-select form-select-sm select2 @error('customer_code') is-invalid @enderror"
                                        style="width: 160px; height: 34px; font-size: 14px; padding: 6px 12px; line-height: 1.5;">
                                        <option value="" disabled selected style="color: #999;">-- පාරිභෝගිකයා තෝරන්න --
                                        </option>
                                        @foreach ($customers as $customer)
                                            <option value="{{ $customer->short_name }}"
                                                data-customer-code="{{ $customer->short_name }}"
                                                data-customer-name="{{ $customer->name }}" {{ old('customer_code_select') == $customer->short_name ? 'selected' : '' }}>
                                                {{ $customer->name }} ({{ $customer->short_name }})
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('customer_code')
                                        <div class="invalid-feedback" style="font-size: 11px;">{{ $message }}</div>
                                    @enderror
                                </div>
                                {{-- Loan Amount Display --}}
                                <div class="col-md-3">
                                    <div class="form-control"
                                        style="width: 80px; height: 34px; font-size: 14px; padding: 6px 12px; border: 1px solid black; color: black; background-color: #f0f0f0; text-align: right;">
                                        <span id="loan_amount_display">0.00</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- GRN Section --}}
                        <div class="row mt-2">
                            <div class="col-md-12">
                                <input type="text" id="grn_display" class="form-control" placeholder="Select GRN Entry..."
                                    readonly
                                    style="height: 45px; font-size: 16px; padding: 8px 16px; display: none; text-align: center !important; border: 1px solid black; color: black; text-transform: uppercase;">
                                <select id="grn_select" class="form-select select2"
                                    style="height: 45px; font-size: 16px; padding: 8px 16px; border: 1px solid black; color: black; text-transform: uppercase;">
                                    <option value="">-- Select GRN Entry --</option>
                                    @foreach ($entries as $entry)
                                        <option value="{{ $entry->code }}" data-supplier-code="{{ $entry->supplier_code }}"
                                            data-code="{{ $entry->code }}" data-item-code="{{ $entry->item_code }}"
                                            data-item-name="{{ $entry->item_name }}" data-weight="{{ $entry->weight }}"
                                            data-price="{{ $entry->price_per_kg }}" data-total="{{ $entry->total }}"
                                            data-packs="{{ $entry->packs }}" data-grn-no="{{ $entry->grn_no }}"
                                            data-txn-date="{{ $entry->txn_date }}"
                                          data-sprice="{{ $entry->SalesKGPrice }}"
                                            data-original-weight="{{ $entry->original_weight }}"
                                            data-original-packs="{{ $entry->original_packs }}">
                                            {{ $entry->code }} | {{ $entry->supplier_code }} | {{ $entry->item_code }} |
                                            {{ $entry->item_name }} | {{ $entry->packs }} | {{ $entry->grn_no }} |
                                            {{ $entry->txn_date }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        {{-- Hidden fields for customer and GRN --}}
                        <input type="hidden" name="customer_name" id="customer_name_hidden"
                            value="{{ old('customer_name') }}">
                        <input type="hidden" name="grn_entry_code" id="grn_entry_code" value="">
                        {{-- Supplier Section (Hidden) --}}
                        <div class="row g-1 form-row mt-2">
                            <div class="col-md-3 mb-1 d-none">
                                <select name="supplier_code_display" id="supplier_code_display"
                                    class="form-select @error('supplier_code') is-invalid @enderror" disabled
                                    style="border: 1px solid black; color: black;">
                                    <option value="" disabled selected>සැපයුම්කරු (Supplier)</option>
                                    @php $currentSupplierCode = old('supplier_code', $sale->supplier_code ?? ''); @endphp
                                    @foreach ($suppliers as $supplier)
                                        <option value="{{ $supplier->code }}" {{ $currentSupplierCode == $supplier->code ? 'selected' : '' }}>
                                            {{ $supplier->name }} ({{ $supplier->code }})
                                        </option>
                                    @endforeach
                                </select>
                                <input type="hidden" name="supplier_code" id="supplier_code"
                                    value="{{ $currentSupplierCode }}">
                                @error('supplier_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-3 mb-1 d-none">
                                <input type="hidden" name="item_code" value="{{ old('item_code') }}">
                                <select id="item_select" class="form-select @error('item_code') is-invalid @enderror"
                                    disabled style="border: 1px solid black; color: black;">
                                    <option value="" disabled selected>අයිතමය තෝරන්න (Select Item)</option>
                                    @foreach ($items as $item)
                                        <option value="{{ $item->item_code }}" data-code="{{ $item->code }}"
                                            data-item-code="{{ $item->item_code }}" data-item-name="{{ $item->item_name }}" {{ old('item_code') == $item->item_code ? 'selected' : '' }}>
                                            ({{ $item->item_code }})
                                        </option>
                                    @endforeach
                                </select>
                                @error('item_code')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        {{-- Item Details Section --}}
                        <div class="d-flex flex-wrap gap-2 align-items-start mt-2">
    <!-- Slightly smaller Item Name field -->
    <div style="flex: 1.5 1 150px;">
        <input type="text" id="item_name_display_from_grn" class="form-control" readonly
            placeholder="අයිතමයේ නම (Item Name)"
            style="background-color: #e9ecef; color: black; height: 45px; font-size: 18px; padding: 6px 10px; border: 1px solid black;">
    </div>
    

  <!-- Weight -->
<div style="width: 100px;">
    <input type="number" name="weight" id="weight" step="0.01"
        class="form-control @error('weight') is-invalid @enderror"
        value="{{ old('weight') }}" placeholder="බර (kg)" required
        style="height: 45px; font-size: 18px; padding: 6px 10px; border: 1px solid black; color: black;">

   <small id="remaining_weight_display" 
       class="form-text text-danger fw-bold"
       style="font-size: 1.1rem; display: block; margin-top: 4px; margin-left: -180px; text-align: left;">
    BW: 0.00
</small>

</div>



 <!-- price_per_kg -->
   <div style="flex: 1 1 80px; position: relative;">
    <input type="number" name="price_per_kg" id="price_per_kg" step="0.01"
        class="form-control @error('price_per_kg') is-invalid @enderror"
        value="{{ old('price_per_kg') }}" placeholder="මිල (Price/kg)" required
        style="height: 45px; font-size: 18px; padding: 6px 10px; border: 1px solid black; color: black;">

    <!-- GRN Price display -->
    <small id="grn_price_display" style="color: red; display: none; font-size: 14px; margin-top: 4px; display: block;"></small>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const grnSelect = $('#grn_select'); 
    const grnPriceDisplay = $('#grn_price_display');

    // Initialize Select2
    grnSelect.select2({
        placeholder: '-- Select GRN Entry --',
        allowClear: true,
        width: '100%'
    });

    // Listen to Select2 change
    grnSelect.on('change', function() {
        const selectedCode = $(this).val();

        if (selectedCode) {
            fetch(`https://talentconnect.lk/AA/sms/grn-entry/${selectedCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.per_kg_price !== null) {
                        grnPriceDisplay.text(`${data.per_kg_price}`).show();
                    } else {
                        grnPriceDisplay.text('').hide();
                    }
                })
                .catch(error => {
                    console.error('Error fetching GRN data:', error);
                    grnPriceDisplay.text('').hide();
                });
        } else {
            grnPriceDisplay.text('').hide();
        }
    });
});
</script>

 

    <!-- Packs -->
    <div style="flex: 1 1 80px;">
        <input type="number" name="packs" id="packs"
            class="form-control @error('packs') is-invalid @enderror" value="{{ old('packs') }}"
            placeholder="ඇසුරුම් (Packs)" required
            style="height: 45px; font-size: 18px; padding: 6px 10px; border: 1px solid black; color: black;">
        <small id="remaining_packs_display" class="form-text text-danger fw-bold"
            style="font-size: 1.3rem;">BP: 0</small>
    </div>

    <!-- Larger Total field -->
    <div style="flex: 1.5 1 120px;">
        <input type="number" name="total" id="total" readonly
            class="form-control bg-light @error('total') is-invalid @enderror"
            value="{{ old('total') }}" placeholder="සමස්ත (Total)"
            style="height: 45px; font-size: 18px; padding: 6px 10px; border: 1px solid black;">
    </div>
</div>
                        <input type="hidden" name="code" id="code" value="{{ old('code') }}">
                        <input type="hidden" name="item_name" id="item_name" value="{{ old('item_name') }}">
                        <input type="hidden" name="original_weight" id="original_weight_input">
                        <input type="hidden" name="original_packs" id="original_packs_input">
                        {{-- Action Buttons --}}
                        <div class="d-grid gap-2 d-md-flex justify-content-center mt-4">
                            <input type="hidden" name="sale_id" id="sale_id">
                            <button type="submit" class="btn btn-primary btn-sm shadow-sm d-none" id="addSalesEntryBtn">
                                <i class="material-icons me-2">add_circle_outline</i>Add Sales Entry
                            </button>
                            <button type="button" class="btn btn-success btn-sm shadow-sm" id="updateSalesEntryBtn"
                                style="display:none;">
                                <i class="material-icons me-2">edit</i>Update Sales Entry
                            </button>
                       

                            <button type="button" class="btn btn-danger btn-sm shadow-sm" id="deleteSalesEntryBtn"
                                style="display:none;">
                                <i class="material-icons me-2">delete</i>Delete Sales Entry
                            </button>
                            <button type="button" class="btn btn-secondary btn-sm shadow-sm" id="cancelEntryBtn"
                                style="display:none;">
                                <i class="material-icons me-2">cancel</i>Cancel / New Entry
                            </button>
                        </div>
                   
                    




             {{-- Main Sales Table - ALWAYS RENDERED --}}
                    <div class="mt-0">
                        <div class="table-responsive">
                            <style>
                                #mainSalesTableBody tr,
                                #mainSalesTableBody td {
                                    background-color: black !important;
                                    color: white !important;
                                }
                            </style>
                           <table class="table table-bordered table-hover shadow-sm mt-3" style="font-size:0.85rem;">
    <thead>
        <tr>
            <th>කේතය</th>
            <th>අයිතමය</th>
            <th>බර (kg)</th>
            <th>මිල</th>
            <th>සමස්ත</th>
            <th>මලු</th>
        </tr>
    </thead>
    <tbody id="mainSalesTableBody">
        @foreach($sales as $sale)
        <tr data-sale='@json($sale)'>
            <td>{{ $sale->code }}</td>
            <td>{{ $sale->item_name }}</td>
            <td>{{ number_format($sale->weight, 2) }}</td>
            <td>{{ number_format($sale->price_per_kg, 2) }}</td>
          <td>{{ number_format($sale->weight * $sale->price_per_kg, 2) }}</td>

            <td>{{ $sale->packs }}</td>
        </tr>
        @endforeach
    </tbody>
</table>
{{-- Textbox for given amount --}}
<div class="mt-3 d-flex justify-content-end">
    <div class="w-25">
       
       <input type="number" step="0.01" name="given_amount" id="given_amount" 
               class="form-control form-control-sm text-end" placeholder="දුන් මුදල">
 
    </div>
</div>


 
                        




                            <h5 style="font-size: 1.5rem; color: red; margin: 0; white-space: nowrap; text-align: right;">
                                <strong>Total Sales Value:</strong> Rs.
                                <span id="mainTotalSalesValueBottom">{{ number_format($totalSum, 2) }}</span>
                            </h5>
                            <div id="itemSummary"></div>
                            <button id="printButton">Print Receipt</button>
                            <button id="f5Button">Hold Receipt</button>
                            <script>
                                document.getElementById('f5Button').addEventListener('click', function (e) {
                                    e.preventDefault(); // prevent any default behavior

                                    if (confirm("Do you want to hold?")) {
                                        // Create a KeyboardEvent simulating F5
                                        const f5Event = new KeyboardEvent('keydown', {
                                            key: 'F5',
                                            code: 'F5',
                                            keyCode: 116, // F5 key code
                                            which: 116, // needed for some browsers
                                            bubbles: true,
                                            cancelable: true
                                        });
                                        // Dispatch the event on the document
                                        document.dispatchEvent(f5Event);
                                        console.log('F5 key simulated!');
                                    } else {
                                        console.log('Hold cancelled by user.');
                                    }
                                });
                            </script>
                            <button id="f10Button">Refresh</button>

<script>
    document.getElementById('f10Button').addEventListener('click', function(e) {
        e.preventDefault(); // prevent any default behavior

        if (confirm("Do you want to Refresh ?")) {
            // Create a KeyboardEvent simulating F10
            const f10Event = new KeyboardEvent('keydown', {
                key: 'F10',
                code: 'F10',
                keyCode: 121, // F10 key code
                which: 121,   // needed for some browsers
                bubbles: true,
                cancelable: true
            });

            // Dispatch the event on the document
            document.dispatchEvent(f10Event);
            console.log('F10 key simulated!');
        } else {
            console.log('F10 trigger cancelled by user.');
        }
    });
</script>

                        </div>
                    </div>
                </div>
            </div>
             </form>
             <script>
document.addEventListener('DOMContentLoaded', function() {
    const salesEntryForm = document.getElementById('salesEntryForm');
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    let submitted = false;

    // --- Select2 Initialization and Event Listener ---
    $('#grn_select').select2({
        placeholder: '-- Select GRN Entry --',
        allowClear: true,
        width: '100%'
    });

    $('#grn_select').on('select2:open', function() {
        const searchBox = document.querySelector('.select2-container--open .select2-search__field');
        if (searchBox) {
            searchBox.focus();
            const len = searchBox.value.length;
            searchBox.setSelectionRange(len, len);
        }
    });
    // --- End Select2 Block ---

    // Handle form submission via AJAX
    salesEntryForm.addEventListener('submit', function(e) {
        e.preventDefault();

        if (submitted) return false;
        submitted = true;

        const submitButton = salesEntryForm.querySelector('button[type="submit"]');
        const originalText = submitButton.innerHTML;
        submitButton.innerHTML = 'Processing...';
        submitButton.disabled = true;

        const formData = new FormData(salesEntryForm);

        fetch(salesEntryForm.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const newSale = data.data;
                console.log("✅ New sale received:", newSale);

                // Push new sale to global data arrays
                window.allSalesData.push(newSale);

                if (newSale.bill_printed) {
                    if (Array.isArray(window.printedSalesData)) {
                        window.printedSalesData.push(newSale);
                    } else {
                        // Handle the case where it's an object keyed by customer code
                        if (!window.printedSalesData[newSale.customer_code]) {
                            window.printedSalesData[newSale.customer_code] = [];
                        }
                        window.printedSalesData[newSale.customer_code].push(newSale);
                    }
                } else {
                    if (Array.isArray(window.unprintedSalesData)) {
                        window.unprintedSalesData.push(newSale);
                    } else {
                        // Handle the case where it's an object keyed by customer code
                        if (!window.unprintedSalesData[newSale.customer_code]) {
                            window.unprintedSalesData[newSale.customer_code] = [];
                        }
                        window.unprintedSalesData[newSale.customer_code].push(newSale);
                    }
                }
                
                // Update the currently displayed table data
                window.currentDisplayedSalesData.push(newSale);
                window.populateMainSalesTable(window.currentDisplayedSalesData);

                // Preserve customer info
                const customerCode = document.getElementById('new_customer_code').value;
                const customerName = document.getElementById('customer_name_hidden').value;

                salesEntryForm.reset();

                document.getElementById('new_customer_code').value = customerCode;
                document.getElementById('customer_name_hidden').value = customerName;

                // Reset GRN select and related fields
                $('#grn_select').val(null).trigger('change');
                document.getElementById('grn_entry_code').value = '';
                document.getElementById('remaining_weight_display').textContent = 'BW: 0.00 kg';
                document.getElementById('remaining_packs_display').textContent = 'BP: 0';
                
                // Re-open GRN Select2 for next entry
                setTimeout(() => {
                    $('#grn_select').select2('open');
                }, 50);

            } else {
                console.error('Submission error:', data.message);
                alert('Error: ' + (data.message || 'Unknown error occurred'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Network error occurred. Please try again.');
        })
        .finally(() => {
            submitButton.innerHTML = originalText;
            submitButton.disabled = false;
            submitted = false;
        });
    });

    // Handle GRN select change to show price
    const grnSelect = $('#grn_select');
    const grnPriceDisplay = $('#grn_price_display');
    grnSelect.on('change', function() {
        const selectedCode = $(this).val();
        if (selectedCode) {
            fetch(`https://talentconnect.lk/AA/sms/grn-entry/${selectedCode}`)
                .then(response => response.json())
                .then(data => {
                    if (data.per_kg_price !== null) {
                        grnPriceDisplay.text(`${data.per_kg_price}`).show();
                    } else {
                        grnPriceDisplay.text('').hide();
                    }
                })
                .catch(error => {
                    console.error('Error fetching GRN data:', error);
                    grnPriceDisplay.text('').hide();
                });
        } else {
            grnPriceDisplay.text('').hide();
        }
    });
});
</script>

            {{-- NEW SECTION: Unprinted Sales Records (bill_printed = 'N') - Right Column --}}
            <div class="col-custom-2-5">
                <div class="card shadow-sm border-0 rounded-3" style="height: 250px;">
                    {{-- Fixed total height --}}
                    <div class="p-3"
                        style="background-color: #004d00; border-top-left-radius: .3rem; border-top-right-radius: .3rem;">
                        <h6 class="mb-2 text-center text-white">
                           මුද්‍රණය නොකළ  වාර්තා
                        </h6>
                        {{-- 🔍 Search Bar --}}
                        <input type="text" id="searchUnprintedCustomerCode" class="form-control form-control-sm mb-2"
                            placeholder="Search by customer code...">
                    </div>

                    {{-- Scrollable list area --}}
                    <div style="flex: 1; overflow-y: auto; padding: 0.5rem; background: #5ed772ff;">
                        @if ($salesNotPrinted->count())
                            <ul id="unprintedSalesList" style="list-style: none; padding-left: 0; margin: 0;">
                                @php
        $sortedSalesNotPrinted = $salesNotPrinted->sortByDesc(function ($salesForCustomer) {
            return $salesForCustomer->max('created_at');
        });
                                @endphp
                                @foreach ($sortedSalesNotPrinted as $customerCode => $salesForCustomer)
                                    @php
            $firstSaleForCustomer = $salesForCustomer->first();
            $customerName = $firstSaleForCustomer->customer_name;
            $totalCustomerSalesAmount = $salesForCustomer->sum('total');
                                    @endphp
                                    <li data-customer-code="{{ $customerCode }}">
                                        <div class="customer-header bill-clickable" data-customer-code="{{ $customerCode }}"
                                            data-customer-name="{{ $customerName }}" data-bill-no="" data-bill-type="unprinted"
                                            style="font-size: 17px; padding: 2px 6px; display: flex; justify-content: space-between; align-items: center; border: 1px solid #ddd; margin-bottom: 3px; border-radius: 4px; background-color: #f9f9f9; cursor: pointer;">
                                            <span style="flex: 1;">
                                                ({{ strtoupper($customerCode) }}) -
                                                Rs.{{ number_format($totalCustomerSalesAmount, 2) }}
                                            </span>
                                            <i class="material-icons arrow-icon" style="font-size: 14px;">keyboard_arrow_right</i>
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @else
                            <div class="alert alert-info text-center m-2">
                                No unprinted sales records found.
                            </div>
                        @endif
                    </div>
                </div>

                {{-- DUPLICATE SECTION: Sales Codes --}}
                <div class="card shadow-sm border-0 rounded-3 p-3 mt-3"
                    style="background-color: #006400 !important; color: white; height: 180px; display: flex; flex-direction: column;">
                    <h6 class="mb-2 text-center" style="flex-shrink: 0;">GRN Codes</h6>
                    <input type="text" id="searchByCode" class="form-control form-control-sm mb-2"
                        placeholder="Search code..." style="flex-shrink: 0; font-size: 12px; padding: 4px 8px;">
                    <ul class="list-group list-group-flush" id="codeList"
                        style="font-size: 17px; overflow-y: auto; flex-grow: 1; margin-bottom: 0;">
                        @foreach ($codes as $c)
                            <li class="list-group-item py-1 px-2" data-code="{{ $c->code }}"
                                style="cursor: pointer; background-color: #f8f9fa;">
                                <a href="{{ route('sales.byCode', $c->code) }}"
                                    style="text-decoration: none; color: #006400; font-weight: 500;">
                                    {{ $c->code }}
                                </a>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
       
    </div>


                {{-- JavaScript Includes (jQuery and Select2 should always be loaded before your custom script that uses
                them)
                --}}
                

                <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
                <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
                 {{-- Fetch customer code of unprocessed sales--}}
        <script>
            document.addEventListener("DOMContentLoaded", function () {
                fetch("{{ url('/get-customer-code') }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.customer_code) {
                            document.getElementById("new_customer_code").value = data.customer_code;
                        }
                    })
                    .catch(error => console.error("Error fetching customer code:", error));
            });
        </script>
                  {{-- Triggering F1 AND F5 BUTTONS --}}
                 <script>
            // These event listeners will only run when the buttons are clicked with a mouse
            document.getElementById('f1Button').addEventListener('click', function () {
                // Ask for confirmation
                if (confirm("Do you want to print?")) {
                    // Simulate the F1 key press
                    const f1Event = new KeyboardEvent('keydown', {
                        key: 'F1',
                        code: 'F1',
                        keyCode: 112, // F1 keyCode
                        bubbles: true
                    });
                    document.dispatchEvent(f1Event);
                    console.log('F1 key simulated!');
                } else {
                    console.log('Print cancelled by user.');
                }
            });

            document.getElementById('f5Button').addEventListener('click', function (e) {
                e.preventDefault(); // stop form submission if inside a form
                if (confirm("Do you want to hold?")) {
                    // Simulate the F5 key press
                    const f5Event = new KeyboardEvent('keydown', {
                        key: 'F5',
                        code: 'F5',
                        keyCode: 116, // F5 keyCode
                        bubbles: true
                    });
                    document.dispatchEvent(f5Event);
                    console.log('F5 key simulated!');
                } else {
                    console.log('Hold cancelled by user.');
                }
            });

        </script>
                {{-- Fetch list of sales codes--}}
                <script>
                    document.getElementById('searchByCode').addEventListener('keyup', function () {
                        const val = this.value.toLowerCase();
                        document.querySelectorAll('#codeList li').forEach(li => {
                            li.style.display = li.getAttribute('data-code').toLowerCase().includes(val) ? '' : 'none';
                        });
                    });
                </script>
                {{-- Fetch loan amount--}}
                <script>
                    $(document).ready(function () {
                        // Global vars to hold last fetched loan amount and customer short name
                        let latestLoanAmount = 0;
                        let latestCustomerShortName = '';

                        function debounce(func, delay) {
                            let timeout;
                            return function (...args) {
                                clearTimeout(timeout);
                                timeout = setTimeout(() => func.apply(this, args), delay);
                            };
                        }

                        function fetchLoanAmount(customerShortName) {
                            if (!customerShortName) {
                                $('#loan_amount_display').text('0.00');
                                latestLoanAmount = 0;
                                latestCustomerShortName = '';
                                return;
                            }

                            let csrfToken = $('meta[name="csrf-token"]').attr('content');

                            $.ajax({
                                url: '{{ route('get.loan.amount') }}',
                                method: 'POST',
                                data: {
                                    _token: csrfToken,
                                    customer_short_name: customerShortName
                                },
                                success: function (response) {
                                    let amount = parseFloat(response.total_loan_amount) || 0;
                                    $('#loan_amount_display').text(amount.toFixed(2));
                                    // Save globally for print handler
                                    latestLoanAmount = amount;
                                    latestCustomerShortName = customerShortName;
                                },
                                error: function (xhr) {
                                    console.error("AJAX error:", xhr.responseText);
                                    $('#loan_amount_display').text('0.00');
                                    latestLoanAmount = 0;
                                    latestCustomerShortName = '';
                                }
                            });
                        }

                        const debouncedFetch = debounce(function () {
                            let val = $('#new_customer_code').val();
                            fetchLoanAmount(val);
                        }, 300);

                        $('#new_customer_code').on('keyup', debouncedFetch);

                        $('#customer_code_select').on('change', function () {
                            let selectedShortName = $(this).val();
                            fetchLoanAmount(selectedShortName);
                        });

                        // F1 print handler using latestLoanAmount & latestCustomerShortName
                        document.addEventListener('keydown', function (e) {
                            if (e.key === "F1") {
                                e.preventDefault();

                                const tableRows = document.querySelectorAll('#mainSalesTableBody tr');
                                if (!tableRows.length || (tableRows.length === 1 && tableRows[0].querySelector('td[colspan="7"]'))) {
                                    alert('No sales records in the table to print!');
                                    return;
                                }

                                const salesData = [];
                                tableRows.forEach(row => {
                                    if (row.hasAttribute('data-sale-id')) {
                                        const cells = row.querySelectorAll('td');
                                        salesData.push({
                                            id: row.getAttribute('data-sale-id'),
                                            customer_code: row.getAttribute('data-customer-code'),
                                            customer_name: row.getAttribute('data-customer-name'),
                                            mobile: row.getAttribute('data-customer-mobile') || '',
                                            code: cells[0]?.textContent.trim() || '',
                                            item_code: cells[1]?.textContent.trim() || '',
                                            item_name: cells[2]?.textContent.trim() || '',
                                            weight: parseFloat(cells[3]?.textContent) || 0,
                                            price_per_kg: parseFloat(cells[4]?.textContent) || 0,
                                            total: parseFloat(cells[5]?.textContent) || 0,
                                            packs: parseInt(cells[6]?.textContent) || 0
                                        });
                                    }
                                });

                                if (!salesData.length) {
                                    alert('No printable sales records found!');
                                    return;
                                }

                                const salesByCustomer = salesData.reduce((acc, sale) => {
                                    (acc[sale.customer_code] ||= []).push(sale);
                                    return acc;
                                }, {});

                                const customerCode = Object.keys(salesByCustomer)[0];

                                // Use loan amount only if customer matches
                                let loanAmountForPrint = 0;
                                if (customerCode === latestCustomerShortName) {
                                    loanAmountForPrint = latestLoanAmount;
                                }

                                // You can now pass loanAmountForPrint into your print template
                                console.log('Loan amount for print:', loanAmountForPrint);

                                // Continue with your existing print logic...

                            }
                        });
                    });
                </script>

                {{-- PASSCODE FOR DELETE BUTTON --}}
                <script>
                    // Get references to the elements
                    const verificationField = document.getElementById('verificationField');
                    const deleteAllButton = document.getElementById('deleteAllButton');
                    const requiredText = 'nethma123'; // The specific text to enable the button

                    // Add an event listener that fires every time the user types
                    verificationField.addEventListener('input', function () {
                        // Check if the input field's current value matches the required text
                        if (this.value === requiredText) {
                            // If it matches, enable the button
                            deleteAllButton.disabled = false;
                        } else {
                            // If it doesn't match, keep the button disabled
                            deleteAllButton.disabled = true;
                        }
                    });
                </script>
                {{-- SCRIPT TO SEARCH THE UNPRINTED SALES RECORDS --}}
                <script>
                    document.addEventListener("DOMContentLoaded", function () {
                        // ... (your existing JavaScript for other functionalities)

                        const searchInput = document.getElementById('searchUnprintedCustomerCode');
                        const unprintedSalesList = document.getElementById('unprintedSalesList');

                        searchInput.addEventListener('keyup', function () {
                            const searchTerm = searchInput.value.toLowerCase();
                            const customerListItems = unprintedSalesList.getElementsByTagName('li');

                            for (let i = 0; i < customerListItems.length; i++) {
                                const listItem = customerListItems[i];
                                const customerCode = listItem.getAttribute('data-customer-code').toLowerCase();

                                if (customerCode.includes(searchTerm)) {
                                    listItem.style.display = ''; // Show the list item
                                } else {
                                    listItem.style.display = 'none'; // Hide the list item
                                }
                            }
                        });
                    });
                </SCRIPT>
                {{-- SCRIPTON SEARCHING BY BILL NO AND CUSTOMER CODE --}}
                <script>
                    const searchInput = document.getElementById('searchPrintedSales');
                    // Get a reference to the list of bills
                    const printedSalesList = document.getElementById('printedSalesList');

                    // Add an event listener for the 'input' event (fires on every keystroke)
                    searchInput.addEventListener('input', function () {
                        // Get the search query and convert it to lowercase for case-insensitive matching
                        const searchQuery = this.value.toLowerCase();

                        // Loop through each customer group (the <li> elements with data-customer-code)
                        const customerGroups = printedSalesList.querySelectorAll('li[data-customer-code]');

                        customerGroups.forEach(customerGroup => {
                            // Assume the entire customer group should be hidden initially
                            let groupHasVisibleBills = false;

                            // Loop through the bills within each customer group
                            const billItems = customerGroup.querySelectorAll('.bill-clickable');

                            billItems.forEach(billItem => {
                                // Get the bill number and customer code from the data attributes
                                const billNo = billItem.dataset.billNo.toLowerCase();
                                const customerCode = billItem.dataset.customerCode.toLowerCase();
                                const customerName = billItem.dataset.customerName.toLowerCase();

                                // Check if the search query is in the bill number or customer code
                                if (billNo.includes(searchQuery) || customerCode.includes(searchQuery) || customerName.includes(searchQuery)) {
                                    // If there's a match, show the bill and mark the group as having visible items
                                    billItem.style.display = 'flex';
                                    groupHasVisibleBills = true;
                                } else {
                                    // If no match, hide the bill
                                    billItem.style.display = 'none';
                                }
                            });

                            // After checking all bills in the group, show or hide the entire group
                            // based on whether any bills within it are visible.
                            if (groupHasVisibleBills) {
                                customerGroup.style.display = 'block';
                            } else {
                                customerGroup.style.display = 'none';
                            }
                        });
                    });
                </script>

                <script>
                    $(document).ready(function () {
                        $('#new_customer_code').on('input', function () {
                            const customerCode = $(this).val().toUpperCase();

                            // Only fetch if the input has a value
                            if (customerCode) {
                                // Construct the URL using the base path and the JavaScript variable
                                const url = `{{ url('/get-unprinted-sales') }}/${customerCode}`;

                                $.ajax({
                                    url: url,
                                    method: 'GET',
                                    success: function (response) {
                                        console.log("Fetched sales data:", response);

                                        // ... (rest of your success function remains the same)
                                    },

                                });
                            } else {
                                // The input field is empty, clear the table and total
                                $('#mainSalesTableBody').empty();
                                $('#mainTotalSalesValue').text('0.00');
                            }
                        });
                    });
                </script>

                <script>

                    document.addEventListener('DOMContentLoaded', function () {

                        function resetFormAndTable() {
                            // Get the list of sales IDs from the table to be marked as unprinted.
                            // This assumes your table rows have a data-sale-id attribute.
                            const saleIds = [];
                            document.querySelectorAll('#mainSalesTableBody tr[data-sale-id]').forEach(row => {
                                saleIds.push(row.dataset.saleId);
                            });

                            // If there are records, send them to the server to be marked as unprinted
                            if (saleIds.length > 0) {
                                console.log('Resetting form - sending unprinted sales to server.');

                                const csrfToken = '{{ csrf_token() }}';
                                const url = '{{ route('sales.save-as-unprinted') }}';

                                const data = new Blob([JSON.stringify({
                                    _token: csrfToken,
                                    sale_ids: saleIds
                                })], {
                                    type: 'application/json'
                                });

                                // Use navigator.sendBeacon for a reliable background request
                                navigator.sendBeacon(url, data);
                            }

                            // After sending the request, proceed with clearing the form and reloading
                            document.getElementById('salesEntryForm').reset();
                            salesEntryForm.action = "{{ route('grn.store') }}";
                            document.getElementById('mainSalesTableBody').innerHTML = '';
                            document.getElementById('new_customer_code').value = '';
                            document.getElementById('customer_name_hidden').value = '';
                            $('#customer_code_select').val(null).trigger('change.select2');
                            document.getElementById('grn_display').style.display = 'none';
                            $('#grn_select').next('.select2-container').show();
                            $('#grn_select').val(null).trigger('change.select2');

                            // Reset the button displays
                            addSalesEntryBtn.style.display = 'inline-block';
                            updateSalesEntryBtn.style.display = 'none';
                            deleteSalesEntryBtn.style.display = 'none';
                            cancelEntryBtn.style.display = 'none';

                            console.log("Form, table, and buttons reset. Reloading page.");
                            location.reload();
                        }

                        // Add an event listener for the F10 key press
                        document.addEventListener('keydown', function (event) {
                            if (event.key === 'F10') {
                                event.preventDefault();
                                resetFormAndTable();
                            }
                        });

                        // Update the click handler for the cancel button to use the new function
                        document.getElementById('cancelEntryBtn').addEventListener('click', function () {
                            resetFormAndTable();
                        });
                    });
                </script>

                {{-- Ensure Bootstrap JS is loaded for collapse --}}
                <script>
                    // ... (existing JavaScript code, including Select2 initializations and other event listeners) ...

                    // NEW: Search functionality for Printed Sales Records
                    document.getElementById('searchCustomerCode').addEventListener('keyup', function () {
                        const searchTerm = this.value.toLowerCase();
                        const printedSalesList = document.getElementById('printedSalesList');
                        const customerGroups = printedSalesList.querySelectorAll('li[data-customer-code]');

                        customerGroups.forEach(customerGroup => {
                            let customerGroupHasVisibleBills = false;
                            const billItems = customerGroup.querySelectorAll('li > .customer-header.bill-clickable');

                            billItems.forEach(billItem => {
                                const billNoElement = billItem.querySelector('span'); // The span containing "Bill No: ..."
                                const billNoText = billNoElement ? billNoElement.textContent.toLowerCase() : '';

                                if (billNoText.includes(searchTerm)) {
                                    billItem.style.display = 'flex'; // Show the bill
                                    customerGroupHasVisibleBills = true;
                                } else {
                                    billItem.style.display = 'none'; // Hide the bill
                                }
                            });

                            // Show/hide the customer group header based on whether any bills within it are visible
                            // You need a way to target the customer group header explicitly.
                            // For now, if no bills are visible, hide the whole customer group li
                            if (customerGroupHasVisibleBills) {
                                customerGroup.style.display = 'block'; // Or 'list-item'
                            } else {
                                customerGroup.style.display = 'none';
                            }
                        });
                    });


                    // ... (rest of your existing JavaScript code) ...
                </script>

                <script>
                    // For Printed Sales (already added earlier)
                    document.getElementById('searchCustomerCode').addEventListener('input', function () {
                        const searchValue = this.value.toLowerCase();
                        document.querySelectorAll('#printedSalesList > li').forEach(li => {
                            const code = li.getAttribute('data-customer-code').toLowerCase();
                            li.style.display = code.includes(searchValue) ? '' : 'none';
                        });
                    });

                    // For Unprinted Sales (this is new)
                    document.getElementById('searchUnprintedCustomerCode').addEventListener('input', function () {
                        const searchValue = this.value.toLowerCase();
                        document.querySelectorAll('#unprintedSalesList > li').forEach(li => {
                            const code = li.getAttribute('data-customer-code').toLowerCase();
                            li.style.display = code.includes(searchValue) ? '' : 'none';
                        });
                    });
                </script>
                <script>
                    // NEW: Search functionality for DUPLICATE Printed Sales Records
                    document.getElementById('searchCustomerCodeDuplicate').addEventListener('keyup', function () {
                        const searchTerm = this.value.toLowerCase();
                        const printedSalesListDuplicate = document.getElementById('printedSalesListDuplicate');
                        const customerGroups = printedSalesListDuplicate.querySelectorAll('li[data-customer-code]');

                        customerGroups.forEach(customerGroup => {
                            let customerGroupHasVisibleBills = false;
                            const billItems = customerGroup.querySelectorAll('li > .customer-header.bill-clickable');

                            billItems.forEach(billItem => {
                                const billNoElement = billItem.querySelector('span');
                                // This includes both customer code and bill number in its text,
                                // allowing search across both.
                                const billNoText = billNoElement ? billNoElement.textContent.toLowerCase() : '';

                                if (billNoText.includes(searchTerm)) {
                                    billItem.style.display = 'flex';
                                    customerGroupHasVisibleBills = true;
                                } else {
                                    billItem.style.display = 'none';
                                }
                            });

                            if (customerGroupHasVisibleBills) {
                                customerGroup.style.display = 'block';
                            } else {
                                customerGroup.style.display = 'none';
                            }
                        });
                    });

                    // For DUPLICATE Printed Sales (this is new, specifically for input event to filter customer groups)
                    // This second listener allows filtering the top-level customer groups directly
                    // if the search term matches the customer code itself.
                    document.getElementById('searchCustomerCodeDuplicate').addEventListener('input', function () {
                        const searchValue = this.value.toLowerCase();
                        document.querySelectorAll('#printedSalesListDuplicate > li').forEach(li => {
                            const customerCode = li.getAttribute('data-customer-code').toLowerCase();
                            // Check if the customer code matches
                            if (customerCode.includes(searchValue)) {
                                li.style.display = ''; // Show the customer group
                                // Also ensure all bills within this group are shown if the customer code matches the search
                                li.querySelectorAll('li > .customer-header.bill-clickable').forEach(billItem => {
                                    billItem.style.display = 'flex';
                                });
                            } else {
                                // If customer code doesn't match, check if any of its bills match
                                let anyBillMatches = false;
                                li.querySelectorAll('li > .customer-header.bill-clickable').forEach(billItem => {
                                    const billNoElement = billItem.querySelector('span');
                                    const billNoText = billNoElement ? billNoElement.textContent.toLowerCase() : '';
                                    if (billNoText.includes(searchValue)) {
                                        anyBillMatches = true;
                                    }
                                });

                                if (anyBillMatches) {
                                    li.style.display = ''; // Show the customer group if any bill matches
                                } else {
                                    li.style.display = 'none'; // Hide if neither customer code nor any bill matches
                                }
                            }
                        });
                    });
                </script>
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        const searchInput = document.getElementById('searchUnprintedCustomerCodeDuplicate');
                        const listItems = document.querySelectorAll('#unprintedSalesListDuplicate li');

                        searchInput.addEventListener('input', function () {
                            const query = this.value.toLowerCase().trim();

                            listItems.forEach(function (li) {
                                const customerCode = li.getAttribute('data-customer-code')?.toLowerCase() || '';
                                li.style.display = customerCode.includes(query) ? 'block' : 'none';
                            });
                        });
                    });
                </script>
                <script>
                    $(document).ready(function () {
                        // Event listener for clicking on a bill in the printed sales list
                        $(document).on('click', '.printed-sales-list .bill-clickable', function () {
                            var billNo = $(this).data('bill-no');
                            var customerCode = $(this).data('customer-code');
                            var customerName = $(this).data('customer-name');

                            // Display the bill number above the customer code input
                            $('#billNoDisplay').text('Bill No: ' + billNo); // THIS LINE IS NEW/MODIFIED

                            // Optionally, set the customer code input and select
                            $('#new_customer_code').val(customerCode);
                            $('#customer_name_hidden').val(customerName);

                            // If you want to update the Select2 dropdown for customer
                            $('#customer_code_select').val(customerCode).trigger('change');
                        });

                        // ... (rest of your existing JavaScript) ...

                    });
                </script>
                {{-- FETCHING THE WEIGHT DETAILS --}}
                <script>
                    document.addEventListener('DOMContentLoaded', function () {
                        console.log('DOM Content Loaded. Initializing script for Add Mode.');

                        // Element references
                        const grnSelect = document.getElementById('grn_select');
                        const weightField = document.getElementById('weight');
                        const packsField = document.getElementById('packs');
                        const remainingWeightDisplay = document.getElementById('remaining_weight_display');
                        const remainingPacksDisplay = document.getElementById('remaining_packs_display');

                        // Other fields (for GRN metadata)
                        const supplierCodeDisplay = document.getElementById('supplier_code_display');
                        const supplierCodeHidden = document.getElementById('supplier_code');
                        const itemSelect = document.getElementById('item_select');
                        const itemCodeHidden = document.querySelector('input[name="item_code"]');
                        const itemNameDisplay = document.getElementById('item_name_display_from_grn');
                        const pricePerKgInput = document.getElementById('price_per_kg');
                        const totalInput = document.getElementById('total');
                        const grnEntryCodeHidden = document.getElementById('grn_entry_code');

                        // Shared global variables
                        let originalGrnPacks = 0;
                        let originalGrnWeight = 0;

                        // --- Function to update the remaining stock in add mode ---
                     function updateRemainingStock() {
                            const currentPacks = parseInt(packsField.value) || 0;
                            const currentWeight = parseFloat(weightField.value) || 0;

                            let remainingPacks = originalGrnPacks - currentPacks;
                            let remainingWeight = originalGrnWeight - currentWeight;

                            // Ensure remaining values don't go below zero
                            if (remainingPacks < 0) remainingPacks = 0;
                            if (remainingWeight < 0) remainingWeight = 0;

                            remainingPacksDisplay.textContent = `BP: ${remainingPacks}`;
                            remainingWeightDisplay.textContent = `BW: ${remainingWeight.toFixed(2)} `;
                        }


                        // --- GRN Change Handler (Modified to use jQuery) ---
                        // Make sure jQuery is available before this part
                        if (window.jQuery && typeof jQuery === 'function') {
                            $(grnSelect).select2();
                            console.log('Select2 initialized.');

                            // Use jQuery's 'change' event listener, which is compatible with Select2
                            $(grnSelect).on('change', function () {
                                const selected = $(this).find('option:selected');
                                if (!selected.length || !selected.val()) {
                                    // ... (rest of the code for resetting fields) ...
                                    return;
                                }

                                // Get original GRN stock values from data attributes
                                originalGrnWeight = parseFloat(selected.data('weight')) || 0;
                                originalGrnPacks = parseInt(selected.data('packs')) || 0;
                                const pricePerKg = parseFloat(selected.data('price')) || 0;

                                // Calculate the total here before assigning it
                                const total = originalGrnWeight * pricePerKg;

                                // Populate other fields
                                supplierCodeDisplay.value = selected.data('supplier-code') || '';
                                supplierCodeHidden.value = selected.data('supplier-code') || '';
                                itemSelect.value = selected.data('item-code') || '';
                                itemCodeHidden.value = selected.data('item-code') || '';
                                itemNameDisplay.value = selected.data('item-name') || '';

                                // This line is now working because `total` is defined.
                                totalInput.value = total.toFixed(2);
                                grnEntryCodeHidden.value = selected.data('code') || '';

                                // Reset inputs to trigger an immediate update of remaining stock
                              
                                

                                // Call the stock update function immediately
                                updateRemainingStock();
                            });
                        } else {
                            // Fallback for when Select2 is not present, using native JS
                            grnSelect?.addEventListener('change', function () {
                                // ... (your previous non-jQuery change handler code here)
                            });
                            console.log('jQuery or Select2 not found. Using native JS event listener.');
                        }

                        // --- Attach listeners for input fields ---
                        packsField.addEventListener('input', updateRemainingStock);
                        weightField.addEventListener('input', updateRemainingStock);

                        // --- Initial page load logic ---
                        // This part should work correctly because it doesn't rely on an event
                        if (grnSelect.value) {
                            const selected = grnSelect.options[grnSelect.selectedIndex];
                            if (selected) {
                                originalGrnWeight = parseFloat(selected.getAttribute('data-original-weight')) || 0;
                                originalGrnPacks = parseInt(selected.getAttribute('data-original-packs')) || 0;
                                grnEntryCodeHidden.value = selected.getAttribute('data-code') || '';
                            }
                        }
                        // Perform the initial calculation and display on page load
                        updateRemainingStock();
                    });
                </script>


                {{-- TYPING THE CUSTOMER_CODE AND FETCHING UNPRINTED SALES --}}


                <!-- Second script block: Main logic -->
         

                {{-- ALL Custom JavaScript Consolidated Here --}}
                <script>
                    // --- Form Calculations & Select2 Interactions ---
                    const itemSelect = document.getElementById('item_select');
                    const codeField = document.getElementById('code');
                    // const itemCodeField = document.getElementById('item_code'); // This is no longer strictly needed if the hidden input inside item_select div is used.
                    const itemNameField = document.getElementById('item_name'); // This is the hidden field
                    const supplierSelect = document.getElementById('supplier_code');
                    const supplierDisplaySelect = document.getElementById('supplier_code_display'); // Add this line
                    const weightField = document.getElementById('weight');
                    const pricePerKgField = document.getElementById('price_per_kg');
                    const totalField = document.getElementById('total');
                    const packsField = document.getElementById('packs');
                    const grnDisplay = document.getElementById('grn_display');


                    const customerSelect = document.getElementById('customer_code_select');
                    const newCustomerCodeField = document.getElementById('new_customer_code');
                    const customerNameField = document.getElementById('customer_name_hidden');
                    newCustomerCodeField.focus();

                    const salesEntryForm = document.getElementById('salesEntryForm');
                    const saleIdField = document.getElementById('sale_id');
                    const addSalesEntryBtn = document.getElementById('addSalesEntryBtn');
                    const updateSalesEntryBtn = document.getElementById('updateSalesEntryBtn');
                    const deleteSalesEntryBtn = document.getElementById('deleteSalesEntryBtn');
                    const cancelEntryBtn = document.getElementById('cancelEntryBtn');

                    // NEW: Get reference to the new item name display field
                    const itemNameDisplayFromGrn = document.getElementById('item_name_display_from_grn');
                    const priceDisplayFromGrn = document.getElementById('price_per_kg');


                    function calculateTotal() {
                        const weight = parseFloat(weightField.value) || 0;
                        const price = parseFloat(pricePerKgField.value) || 0;
                        totalField.value = (weight * price).toFixed(2);
                    }

                    // This listener is mostly for internal consistency if itemSelect.value is set programmatically.
                    // The main item_name population will now come from grn_select.
                    itemSelect.addEventListener('change', function () {
                        const selected = this.options[this.selectedIndex];
                        if (selected && selected.dataset) {
                            codeField.value = selected.dataset.code || '';
                            // itemNameField.value = selected.dataset.itemName || ''; // We will now get item_name from GRN select
                            // Ensure the hidden item_code is updated when item_select value changes programmatically
                            document.querySelector('input[name="item_code"]').value = selected.dataset.itemCode || '';
                        } else {
                            codeField.value = '';
                            // itemNameField.value = ''; // We will now get item_name from GRN select
                            document.querySelector('input[name="item_code"]').value = '';
                        }
                    });


                    weightField.addEventListener('input', calculateTotal);
                    pricePerKgField.addEventListener('input', calculateTotal);
                    calculateTotal(); // Initial calculation on page load

                   $(document).ready(function () {
    // Initialize Select2 for GRN with custom templateResult and templateSelection
 $('#grn_select').select2({
    dropdownParent: $('#grn_select').parent(),
    placeholder: "-- Select GRN Entry --",
    width: '100%',
    allowClear: true,
    minimumResultsForSearch: 0, // Enable search

    // Custom matcher: only show results when user types
    matcher: function(params, data) {
        if (!params.term || params.term.trim() === '') return null;
        return data.text.toUpperCase().includes(params.term.toUpperCase()) ? data : null;
    },

    // Dropdown option template
    templateResult: function (data) {
        if (data.loading || !data.id) return data.text;

        const option = $(data.element);
        const code = option.data('code');
        const sp = option.data('sprice');
        const itemName = option.data('itemName');
        const packs = option.data('packs');
        const weight = option.data('weight');
        const originalWeight = option.data('originalWeight');
        const originalPacks = option.data('originalPacks');
        const txnDate = option.data('txnDate');

        let formattedDate = '';
        if (txnDate) {
            const d = new Date(txnDate);
            if (!isNaN(d)) {
                formattedDate = `${String(d.getMonth()+1).padStart(2,'0')}-${String(d.getDate()).padStart(2,'0')}`;
            }
        }

        return $(`
            <div class="grn-option-row">
                <div class="grn-column grn-code"><strong>${code || ''}</strong></div>
                <div class="grn-column grn-sp">${sp || ''}</div>
                <div class="grn-column grn-item">${itemName || ''}</div>
                <div class="grn-column grn-ow">${originalWeight || ''}</div>
                <div class="grn-column grn-op">${originalPacks || ''}</div>
                <div class="grn-column grn-bw">${weight || ''}</div>
                <div class="grn-column grn-bp">${packs || 0}</div>
               
            </div>
        `);
    },

    // Selected option template
    templateSelection: function (data) {
        if (!data.id) return data.text;
        const option = $(data.element);
        const code = option.data('code');
        const sp = option.data('sprice');
        const originalWeight = option.data('originalWeight');
        const originalPacks = option.data('originalPacks');
     

        return $('<span>')
            .addClass('select2-black-text')
            .css('text-align', 'center')
            .html(`${code || ''} (SP: ${sp || ''} /කිලෝ: ${originalWeight || 0} / මලු: ${originalPacks || ''})`);
    }
});

// Add header to dropdown and handle search
$('#grn_select').on('select2:open', function () {
    const $dropdown = $('.select2-dropdown');
    const searchInput = $dropdown.find('.select2-search__field');

    // Focus and uppercase input
    searchInput.focus().off('input.lazySearch').on('input.lazySearch', function () {
        this.value = this.value.toUpperCase();
        $('#grn_select').trigger('select2:open'); // Refresh dropdown
    });

    // Add header once
    if ($dropdown.find('.grn-header-row').length === 0) {
        const $header = $(`
            <div class="grn-header-row">
                <div class="grn-column grn-code">Code</div>
                <div class="grn-column grn-sp">SP</div>
                <div class="grn-column grn-item">Item</div>
                <div class="grn-column grn-ow">OW</div>
                <div class="grn-column grn-op">OP</div>
                <div class="grn-column grn-bw">BW</div>
                <div class="grn-column grn-bp">BP</div>
               
            </div>
        `);
        $dropdown.find('.select2-results').prepend($header);
    }
});



                        $('#customer_code_select').select2({
                            dropdownParent: $('#customer_code_select').parent(),
                            placeholder: "-- Select Customer --",
                            width: '100%',
                            allowClear: true,
                            templateResult: function (data) {
                                if (data.loading) return data.text;
                                if (!data.id) return data.text;
                                return $(
                                    `<span>${$(data.element).data('customer-name')} (${$(data.element).data('customer-code')})</span>`
                                );
                            },
                            templateSelection: function (data) {
                                if (!data.id) return data.text; // Return placeholder text if nothing is selected
                                return $(
                                    `<span>${$(data.element).data('customer-name')} (${$(data.element).data('customer-code')})</span>`
                                );
                            }
                        });


                        // Handle click on grn_display to open Select2 dropdown
                        $('#grn_display').on('click', function () {
                            $('#grn_select').select2('open');
                        });

                        // Event listener for when a Select2 option is selected for GRN
                        $('#grn_select').on('select2:select', function (e) {
                            const selectedOption = $(e.params.data.element); // Get the raw <option> element
                            const data = selectedOption.data();
                            // Access its data attributes

                            // Update the read-only grn_display field with the formatted string
                            const grnCodeForDisplay = data.code || '';
                            const supplierCodeForDisplay = data.supplierCode || '';
                            const itemCodeForDisplay = data.itemCode || '';
                            const itemNameForDisplay = data.itemName || '';
                            const itempriceForDisplay = data.sprice || '';
                            const packsForDisplay = data.packs || '';
                            const grnNoForDisplay = data.grnNo || '';
                            const txnDateForDisplay = data.txnDate || '';
                            
                            grnDisplay.value =
                                `${grnCodeForDisplay}| ${supplierCodeForDisplay}  | ${packsForDisplay} | ${grnNoForDisplay}`;

                            // Populate other form fields using the data attributes
                            supplierSelect.value = data.supplierCode || ''; // Hidden input for supplier_code
                            supplierDisplaySelect.value = data.supplierCode || ''; // Display select for supplier_code

                            itemSelect.value = data.itemCode || ''; // Set item code in disabled select
                            
                            itemSelect.dispatchEvent(new Event('change')); // Trigger change to update hidden item_code

                            itemNameDisplayFromGrn.value = data.itemName || ''; // Populate the dedicated item name display field
                             priceDisplayFromGrn.value = data.sprice || '';
                            itemNameField.value = data.itemName || '';
                            // Also set the hidden item_name field

                        
                           

                            // ADDED: Populate hidden fields for original_weight and original_packs
                            $('#original_weight_input').val(data.originalWeight); // Access using camelCase
                            $('#original_packs_input').val(data.originalPacks);   // Access using camelCase

                            calculateTotal();
                            weightField.focus();
                        });
                        $('#customer_code_select').on('select2:select', function (e) {
                            const selectedOption = $(e.currentTarget).find('option:selected');
                            const selectedCustomerCode = selectedOption.val();
                            const selectedCustomerName = selectedOption.data('customer-name');

                            newCustomerCodeField.value = selectedCustomerCode || '';
                            newCustomerCodeField.readOnly = true;
                            customerNameField.value = selectedCustomerName || '';

                            $('#grn_select').select2('open');
                        });

                        newCustomerCodeField.addEventListener('keydown', function (event) {
                            if (event.key === 'Enter') {
                                event.preventDefault();
                                $('#grn_select').select2('open');
                            }
                        });
                        $('#grn_select').on('select2:open', function() {
    // Find the search input element created by Select2
    const searchInput = $('.select2-search__field');
    
    // Add an event listener to this search input
    searchInput.on('input', function() {
        // Convert the typed value to uppercase and update the input field
        this.value = this.value.toUpperCase();
    });
});
                        // Clear GRN selection and related fields
                        $('#grn_select').on('select2:clear', function () {
                            grnDisplay.value = 'Select GRN Entry...'; // Reset display field placeholder
                            supplierSelect.value = '';
                            supplierDisplaySelect.value = ''; // Clear display select
                            itemSelect.value = '';
                            itemSelect.dispatchEvent(new Event('change')); // Clear item related hidden fields
                            itemNameDisplayFromGrn.value = ''; // NEW: Clear the item name display field
                            itemNameField.value = ''; // NEW: Clear hidden item_name field
                          
                          
                           
                            calculateTotal();
                        });

                        $('#customer_code_select').on('select2:clear', function () {
                            newCustomerCodeField.value = '';
                            newCustomerCodeField.readOnly = false;
                            customerNameField.value = '';
                        });

                        $('#new_customer_code').on('input', function () {
                            if ($(this).val() !== '') {
                                $('#customer_code_select').val(null).trigger('change');
                                customerNameField.value = '';
                            }
                        });

                        // Handle old input values on page load
                        $(document).ready(function () {
                            $(document).on('select2:open', function () {
                                const searchField = document.querySelector('.select2-search__field');
                                if (searchField) {
                                    searchField.focus();
                                }
                            });

                            @if (old('customer_code_select') || old('customer_code'))
                                const oldGrnCode = "{{ old('code') }}";
                                const oldSupplierCode = "{{ old('supplier_code') }}";
                                const oldItemCode = "{{ old('item_code') }}";
                                const oldItemName = "{{ old('item_name') }}";
                                const oldWeight = "{{ old('weight') }}";
                                const oldPricePerKg = "{{ old('price_per_kg') }}";
                                const oldPacks = "{{ old('packs') }}";
                                const oldGrnOption = $('#grn_select option').filter(function () {
                                    return $(this).val() === oldGrnCode &&
                                        $(this).data('supplierCode') === oldSupplierCode &&
                                        $(this).data('itemCode') === oldItemCode;
                                });

                                if (oldGrnOption.length) {
                                    $('#grn_select').val(oldGrnOption.val()).trigger('change.select2');
                                    grnDisplay.value = oldGrnOption.data('code') || '';
                                    itemNameDisplayFromGrn.value = oldGrnOption.data('itemName') || '';
                                    itemNameField.value = oldGrnOption.data('itemName') || '';
                                    $('#weight').val(oldWeight);
                                    $('#price_per_kg').val(oldPricePerKg);
                                    $('#packs').val(oldPacks);
                                    calculateTotal();
                                }

                                const newCustomerCodeField = document.getElementById('new_customer_code');
                                const customerNameField = document.getElementById('customer_name_hidden');

                                const oldSelectedCustomerCode = "{{ old('customer_code_select') }}";
                                const oldEnteredCustomerCode = "{{ old('customer_code') }}";
                                const oldCustomerNameValue = "{{ old('customer_name') }}";

                                if (oldSelectedCustomerCode) {
                                    $('#customer_code_select').val(oldSelectedCustomerCode).trigger('change');
                                    if (newCustomerCodeField) {
                                        newCustomerCodeField.value = oldSelectedCustomerCode;
                                        newCustomerCodeField.readOnly = true;
                                    }
                                    if (customerNameField) {
                                        customerNameField.value = oldCustomerNameValue;
                                    }
                                } else if (oldEnteredCustomerCode) {
                                    if (newCustomerCodeField) {
                                        newCustomerCodeField.value = oldEnteredCustomerCode;
                                        newCustomerCodeField.readOnly = false;
                                    }
                                    if (customerNameField) {
                                        customerNameField.value = oldCustomerNameValue;
                                    }
                                }

                                $('#grn_select').select2('open');
                            @endif
                                                                                                                                                                                                                                                                                                                                    });
                        function populateSalesTable(salesArray) {
                            const tableBody = document.getElementById('mainSalesTableBody');
                            tableBody.innerHTML = ''; // Clear existing rows

                            if (!salesArray || salesArray.length === 0) {
                                tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No sales records found.</td></tr>';
                                return;
                            }

                            salesArray.forEach(sale => {
                                const row = document.createElement('tr');
                                // --- CRITICAL: Add data- attributes to the row for easy retrieval by F1 function ---
                                row.setAttribute('data-sale-id', sale.id);
                                row.setAttribute('data-customer-code', sale.customer_code);
                                row.setAttribute('data-customer-name', sale.customer_name || 'N/A'); // Ensure customer_name exists

                                row.innerHTML = `
                                                                                                                                                                                                                                                                                                                                        <td>${sale.code}</td>
                                                                                                                                                                                                                                                                                                                                        <td>${sale.item_code}</td>
                                                                                                                                                                                                                                                                                                                                        <td>${sale.item_name}</td>
                                                                                                                                                                                                                                                                                                                                        <td>${(parseFloat(sale.weight) || 0).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                                                                        <td>${(parseFloat(sale.price_per_kg) || 0).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                                                                        <td>${(parseFloat(sale.total) || 0).toFixed(2)}</td>
                                                                                                                                                                                                                                                                                                                                        <td>${sale.packs}</td>
                                                                                                                                                                                                                                                                                                                                    `;
                                tableBody.appendChild(row);
                            });
                        }


let globalLoanAmount = 0;

// Reusable print function
function printReceipt(html, customerName) {
    return new Promise((resolve) => {
        const printWindow = window.open('', '_blank');
        printWindow.document.write(`
            <!DOCTYPE html>
            <html>
            <head>
                <title>${customerName} - Receipt</title>
            </head>
            <body>
                ${html}
            </body>
            </html>
        `);
        printWindow.document.close();
        printWindow.focus();
        printWindow.print();
        setTimeout(() => {
            printWindow.close();
            resolve();
        }, 500);
    });
}



async function handlePrint() {
    // Use the currentDisplayedSalesData instead of scraping table rows
    const salesData = window.currentDisplayedSalesData || [];

    if (!salesData.length) {
        alert('No sales records to print!');
        return;
    }

    const salesIds = salesData.map(s => s.id);

    // Send sales data to backend to get bill number and mark as printed
    const response = await fetch("{{ route('sales.markAsPrinted') }}", {
        method: 'POST',
        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
        body: JSON.stringify({ sales_ids: salesIds })
    });

    const backendResponse = await response.json();
    if (backendResponse.status !== 'success') {
        alert('Failed to process print request.');
        console.error('Backend error:', backendResponse.message);
        return;
    }

    const billNo = backendResponse.bill_no;

    // Group sales by customer
    const salesByCustomer = salesData.reduce((acc, sale) => {
        (acc[sale.customer_code] ||= []).push(sale);
        return acc;
    }, {});

    const customerCode = Object.keys(salesByCustomer)[0];
    const customerSales = salesByCustomer[customerCode];
    const customerName = customerSales[0].customer_code || 'N/A';
    const mobile = customerSales[0].mobile || '0773358518';
    const recipientEmails = ["thrcorner@gmail.com", "nethmavilhan2005@gmail.com"];
    
    try {
        // Fetch loan amount
        const loanResponse = await fetch('{{ route('get.loan.amount') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ customer_short_name: customerCode })
        });
        
        const loanData = await loanResponse.json();
        const globalLoanAmount = parseFloat(loanData.total_loan_amount) || 0;

        const date = "{{ $billDate }}";
        const time = new Date().toLocaleTimeString();
        let totalAmountSum = 0;
        const itemGroups = {};
        let totalPacksSum = 0;

        // SUM ALL given_amounts for this customer
        const givenAmount = customerSales.reduce((sum, sale) => sum + (parseFloat(sale.given_amount) || 0), 0);

        // Build items HTML
        const itemsHtml = customerSales.map(sale => {
            totalAmountSum += parseFloat(sale.total) || 0;
            const itemName = sale.item_name || '';
            const weight = parseFloat(sale.weight) || 0;
            const packs = parseInt(sale.packs) || 0;
            totalPacksSum += packs;

            if (!itemGroups[itemName]) itemGroups[itemName] = { totalWeight: 0, totalPacks: 0 };
            itemGroups[itemName].totalWeight += weight;
            itemGroups[itemName].totalPacks += packs;

            return `<tr style="font-size: 1.2em;">
                <td style="text-align:left;">${itemName} <br>${packs}</td>
                <td style="text-align:right; padding-right:18px;">${weight.toFixed(2)}</td>
                <td style="text-align:right;">${(parseFloat(sale.price_per_kg) || 0).toFixed(2)}</td>
                <td style="text-align:right;">${(parseFloat(sale.total) || 0).toFixed(2)}</td>
            </tr>`;
        }).join('');

        // Build item summary HTML
        let itemSummaryHtml = '';
        const entries = Object.entries(itemGroups);
        for (let i = 0; i < entries.length; i += 2) {
            const first = entries[i];
            const second = entries[i + 1];

            itemSummaryHtml += '<div style="display:flex; gap:0.5rem; margin-bottom:0.2rem;">';
            itemSummaryHtml += `<span style="padding:0.1rem 0.3rem;border-radius:0.5rem;background-color:#f3f4f6;font-size:0.6rem;display:inline-block;">
                                    <strong>${first[0]}</strong>:${first[1].totalWeight.toFixed(2)}/${first[1].totalPacks}
                                </span>`;
            if (second) {
                itemSummaryHtml += `<span style="padding:0.1rem 0.3rem;border-radius:0.5rem;background-color:#f3f4f6;font-size:0.6rem;display:inline-block;">
                                        <strong>${second[0]}</strong>:${second[1].totalWeight.toFixed(2)}/${second[1].totalPacks}
                                    </span>`;
            }
            itemSummaryHtml += '</div>';
        }

        const packCostTotal = window.globalTotalPackCostValue || 0;
        const totalPrice = totalAmountSum;

        // Correct calculations
        const remaining = givenAmount - (totalPrice + packCostTotal); // ඉතිරිය
        const totalWithLoan = globalLoanAmount + remaining; // Loan + remaining

        // GIVEN AMOUNT ROW
        const givenAmountRow = givenAmount > 0
            ? `<tr>
               <td style="width: 50%; text-align: left; white-space: nowrap;">
    <span style="font-size: 0.75rem;">දුන් මුදල: </span>
    <span style="font-weight: bold; font-size: 0.9rem;">${givenAmount.toFixed(2)}</span>
</td>

                <td style="width: 50%; text-align: right; white-space: nowrap; font-size: 1rem;">
                    <span style="font-size: 0.8rem;">ඉතිරිය: </span>
                    <span style="font-weight: bold; font-size: 1.5rem;">
                        ${Math.abs(remaining).toFixed(2)}
                    </span>
                </td>
              </tr>` 
            : '';

        // LOAN ROW
        const loanRow = globalLoanAmount > 0
            ? `<tr>
                <td style="font-weight: normal; font-size: 0.7rem; text-align: left;">
                    පෙර ණය : <span>${globalLoanAmount.toFixed(2)}</span>
                </td>
                <td style="font-weight: bold; text-align: right; font-size: 1.5em;">
                     ${globalLoanAmount+totalPrice + packCostTotal}
                </td>
              </tr>`
            : '';

        // Build receipt HTML
        const receiptHtml = `<div class="receipt-container" style="width: 100%; max-width: 300px; margin: 0 auto; padding: 5px;">
            <!-- HEADER -->
            <div style="text-align: center; margin-bottom: 5px;">
                <h3 style="font-size: 1.8em; font-weight: bold; margin: 0;">
                    <span style="border: 2px solid #000; padding: 0.1em 0.3em; display: inline-block; margin-right: 5px;">B32</span>
                    TAG ට්‍රේඩර්ස්
                </h3>
                <p style="margin: 0; font-size: 0.7em;">අල, ෆී ළූනු, කුළුබඩු තොග ගෙන්වන්නෝ බෙදාහරින්නෝ</p>
                <p style="margin: 0; font-size: 0.7em;">වි.ආ.ම. වේයන්ගොඩ</p>
            </div>

            <!-- CUSTOMER INFO -->
            <div style="text-align: left; margin-bottom: 5px;">
                <table style="width: 100%; font-size: 9px; border-collapse: collapse;">
                    <tr>
                        <td style="width: 50%;">දිනය : ${date}</td>
                        <td style="width: 50%; text-align: right;">${time}</td>
                    </tr>
                    <tr>
                        <td colspan="2">දුර : ${mobile}</td>
                    </tr>
                    <tr>
                        <td>බිල් අංකය : <strong>${billNo}</strong></td>
                        <td style="text-align: right;">
                            <strong style="font-size: 1.8em;">${customerName.toUpperCase()}</strong>
                        </td>
                    </tr>
                </table>
            </div>

            <hr style="border: 0.5px solid #000; margin: 5px 0;">

            <!-- ITEMS -->
            <table style="width: 100%; font-size: 9px; border-collapse: collapse;">
                <thead style="font-size: 1.5em;">
                    <tr>
                        <th style="text-align: left; padding: 2px;">වර්ගය<br>මලු</th>
                        <th style="padding: 2px;">කිලෝ</th>
                        <th style="padding: 2px;">මිල</th>
                        <th style="text-align: right; padding: 2px;">අගය</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td colspan="4">
                            <hr style="height: 1px; background-color: #000; margin: 2px 0;">
                        </td>
                    </tr>

                    ${itemsHtml}

                    <tr>
                        <td colspan="4">
                            <hr style="border: 0.5px solid #000; margin: 5px 0;">
                        </td>
                    </tr>

                    <tr>
                        <td colspan="2" style="text-align: left; font-weight: bold; font-size: 1.2em;">
                            ${totalPacksSum}
                        </td>
                        <td colspan="2" style="text-align: right; font-weight: bold; font-size: 1.2em;">
                            ${totalPrice.toFixed(2)}
                        </td>
                    </tr>
                </tbody>
            </table>

            <!-- SUMMARY -->
            <table style="width: 100%; font-size: 11px; border-collapse: collapse;">
                <tr>
                    <td>ප්‍රවාහන ගාස්තු:</td>
                    <td style="text-align: right; font-weight: bold;">00</td>
                </tr>
                <tr>
                    <td>කුලිය:</td>
                    <td style="text-align: right; font-weight: bold;">${packCostTotal.toFixed(2)}</td>
                </tr>
                <tr>
                    <td>අගය:</td>
                    <td style="text-align: right; font-weight: bold;">
                        <span style="display: inline-block; border-top: 1px solid #000; border-bottom: 3px double #000; padding: 2px 4px; min-width: 80px; text-align: right;">
                            ${(totalPrice + packCostTotal).toFixed(2)}
                        </span>
                    </td>
                </tr>
                ${givenAmountRow}
                ${loanRow}
            </table>

            <hr style="border: 0.5px solid #000; margin: 5px 0;">

            <div style="font-size: 10px;">${itemSummaryHtml}</div>

            <div style="text-align: center; margin-top: 10px; font-size: 10px;">
                <p style="margin: 0;">භාණ්ඩ පරීක්ෂාකර බලා රැගෙන යන්න</p>
                <p style="margin: 0;">නැවත භාර ගනු නොලැබේ</p>
            </div>
        </div>`;

        // Create duplicate with COPY
        const duplicateHtml = `<div style="text-align:center;font-size:2em;font-weight:bold;color:red;margin-bottom:10px;">COPY</div>` + receiptHtml;

        await Promise.all([
            printReceipt(receiptHtml, customerName),
            printReceipt(duplicateHtml, customerName + ' - Copy'),
        ]);

    } catch (err) {
        console.error('An error occurred during loan fetch or printing:', err);
    } finally {
        window.location.reload();
    }
}


// F5 function (Email only, no bill number)
async function handleF5() {
    const tableRows = document.querySelectorAll('#mainSalesTableBody tr');
    if (!tableRows.length || (tableRows.length === 1 && tableRows[0].querySelector('td[colspan="7"]'))) {
        alert('No sales records to process!');
        return;
    }

    const salesData = [];
    tableRows.forEach(row => {
        if (row.hasAttribute('data-sale-id')) {
            const cells = row.querySelectorAll('td');
            salesData.push({
                id: row.getAttribute('data-sale-id'),
                customer_code: row.getAttribute('data-customer-code'),
                customer_name: row.getAttribute('data-customer-name'),
                mobile: row.getAttribute('data-customer-mobile') || '',
                email: "thrcorner@gmail.com",
                code: cells[0]?.textContent.trim() || '',
                item_code: cells[1]?.textContent.trim() || '',
                item_name: cells[1]?.textContent.trim() || '',
                weight: parseFloat(cells[2]?.textContent) || 0,
                price_per_kg: parseFloat(cells[3]?.textContent) || 0,
                total: parseFloat(cells[4]?.textContent) || 0,
                packs: parseInt(cells[5]?.textContent) || 0
            });
        }
    });

    if (!salesData.length) {
        alert('No sales records to process!');
        return;
    }

    const salesByCustomer = salesData.reduce((acc, sale) => {
        (acc[sale.customer_code] ||= []).push(sale);
        return acc;
    }, {});

    const customerCode = Object.keys(salesByCustomer)[0];
    const customerSales = salesByCustomer[customerCode];
    const customerName = customerSales[0].customer_code || 'N/A';
    const mobile = customerSales[0]?.mobile || '-';
    const recipientEmails = ["thrcorner@gmail.com", "nethmavilhan2005@gmail.com"];

    let totalAmountSum = 0;
    const itemGroups = {};

    const itemsHtml = customerSales.map(sale => {
        totalAmountSum += sale.total;
        const itemName = sale.item_name || '';
        const weight = parseFloat(sale.weight) || 0;
        const packs = parseInt(sale.packs) || 0;
        if (!itemGroups[itemName]) itemGroups[itemName] = { totalWeight: 0, totalPacks: 0 };
        itemGroups[itemName].totalWeight += weight;
        itemGroups[itemName].totalPacks += packs;
        return `<tr>
                    <td style="text-align:left;">${itemName} <br>${packs}</td>
                    <td style="text-align:right;">${weight.toFixed(2)}</td>
                    <td style="text-align:right;">${sale.price_per_kg.toFixed(2)}</td>
                    <td style="text-align:right;">${sale.total.toFixed(2)}</td>
                </tr>`;
    }).join('');

    let itemSummaryHtml = '';
    const entries = Object.entries(itemGroups);
    for (let i = 0; i < entries.length; i += 2) {
        const first = entries[i];
        const second = entries[i + 1];
        itemSummaryHtml += '<div style="display:flex; gap:0.5rem; margin-bottom:0.2rem;">';
        itemSummaryHtml += `<span style="padding:0.1rem 0.3rem;border-radius:0.5rem;background-color:#f3f4f6;font-size:0.6rem;display:inline-block;"><strong>${first[0]}</strong>:${first[1].totalWeight.toFixed(2)}/${first[1].totalPacks}</span>`;
        if (second) {
            itemSummaryHtml += `<span style="padding:0.1rem 0.3rem;border-radius:0.5rem;background-color:#f3f4f6;font-size:0.6rem;display:inline-block;"><strong>${second[0]}</strong>:${second[1].totalWeight.toFixed(2)}/${second[1].totalPacks}</span>`;
        }
        itemSummaryHtml += '</div>';
    }

    try {
        const loanRes = await fetch('{{ route('get.loan.amount') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
            body: JSON.stringify({ customer_short_name: customerCode })
        });
       

        
        // Mark all as processed AFTER email
        const processedRes = await fetch('{{ route('sales.markAllAsProcessed') }}', {
            method: 'POST',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const processedData = await processedRes.json();
        console.log('F5 processed:', processedData);

        window.location.reload();

    } catch (err) {
        console.error('F5 error:', err);
        alert('Error processing F5 request');
    }
}

// Keyboard events for F1 & F5
document.addEventListener('keydown', e => {
    if (e.key === "F1") { e.preventDefault(); handlePrint(); }
    else if (e.key === "F5") { e.preventDefault(); handleF5(); }
});

// Optional buttons
document.getElementById('printButton')?.addEventListener('click', function () {
    if (confirm("Do you want to print?")) handlePrint();
});
document.getElementById('f5Button')?.addEventListener('click', function() {
    if (confirm("Do you want to hold this receipt?")) handleF5();
});




                        function printReceipt(salesContent, customerName, onCompleteCallback) {
                            const printWindow = window.open('', '', 'width=300,height=600');

                            printWindow.document.write(`
                                                                                                                                                            <html>
                                                                                                                                                                <head>
                                                                                                                                                                    <title>විකුණුම් කුපිත්තුව - ${customerName}</title>
                                                                                                                                                                    <style>
                                                                                                                                                                        @import url('https://fonts.googleapis.com/css2?family=Noto+Sans+Sinhala:wght@400;700&display=swap');
                                                                                                                                                                        body {
                                                                                                                                                                            font-family: 'Noto Sans Sinhala', sans-serif;
                                                                                                                                                                            margin: 0;
                                                                                                                                                                            padding: 5mm;
                                                                                                                                                                            font-size: 10px;
                                                                                                                                                                            line-height: 1.2;
                                                                                                                                                                            overflow: hidden;
                                                                                                                                                                        }
                                                                                                                                                                        .receipt-container {
                                                                                                                                                                            width: 100%;
                                                                                                                                                                            max-width: 70mm;
                                                                                                                                                                            margin-left: 0;
                                                                                                                                                                            margin-right: auto;
                                                                                                                                                                            border: none;
                                                                                                                                                                            padding: 0;
                                                                                                                                                                            text-align: left;
                                                                                                                                                                        }
                                                                                                                                                                        .company-info {
                                                                                                                                                                            text-align: left;
                                                                                                                                                                            margin-bottom: 5px;
                                                                                                                                                                        }
                                                                                                                                                                        .company-info h3 {
                                                                                                                                                                            font-size: 1.2em;
                                                                                                                                                                            margin-bottom: 2px;
                                                                                                                                                                            font-weight: bold;
                                                                                                                                                                        }
                                                                                                                                                                        .company-info p {
                                                                                                                                                                            margin: 0;
                                                                                                                                                                            line-height: 1.2;
                                                                                                                                                                        }
                                                                                                                                                                        .bill-details, .summary-section, .footer-section {
                                                                                                                                                                            text-align: left;
                                                                                                                                                                            margin-bottom: 5px;
                                                                                                                                                                        }
                                                                                                                                                                        .bill-details p, .summary-section p {
                                                                                                                                                                            margin: 0;
                                                                                                                                                                            line-height: 1.2;
                                                                                                                                                                            display: flex;
                                                                                                                                                                            justify-content: space-between;
                                                                                                                                                                        }
                                                                                                                                                                        .bill-details p span:first-child, .summary-section p span:first-child {
                                                                                                                                                                            text-align: left;
                                                                                                                                                                            font-weight: normal;
                                                                                                                                                                        }
                                                                                                                                                                        .bill-details p span:last-child, .summary-section p span:last-child {
                                                                                                                                                                            text-align: right;
                                                                                                                                                                            font-weight: bold;
                                                                                                                                                                        }
                                                                                                                                                                        .customer-name-on-bill {
                                                                                                                                                                            text-align: left;
                                                                                                                                                                            font-weight: bold;
                                                                                                                                                                            margin-top: 5px;
                                                                                                                                                                        }
                                                                                                                                                                        .divider {
                                                                                                                                                                            border-top: 1px dashed #000;
                                                                                                                                                                            margin: 8px 0;
                                                                                                                                                                        }
                                                                                                                                                                        .items-section table {
                                                                                                                                                                            width: 100%;
                                                                                                                                                                            border-bottom: none;
                                                                                                                                                                            font-size: 10px;
                                                                                                                                                                        }
                                                                                                                                                                        .items-section th, .items-section td {
                                                                                                                                                                            padding: 2px 0;
                                                                                                                                                                            text-align: right;
                                                                                                                                                                            border-bottom: none;
                                                                                                                                                                        }
                                                                                                                                                                        .items-section th {
                                                                                                                                                                            font-weight: bold;
                                                                                                                                                                            text-align: center;
                                                                                                                                                                        }
                                                                                                                                                                        .col-item {
                                                                                                                                                                            text-align: left;
                                                                                                                                                                            width: 40%;
                                                                                                                                                                        }
                                                                                                                                                                        .col-qty {
                                                                                                                                                                            width: 20%;
                                                                                                                                                                        }
                                                                                                                                                                        .col-rate {
                                                                                                                                                                            width: 20%;
                                                                                                                                                                        }
                                                                                                                                                                        .col-value {
                                                                                                                                                                            width: 20%;
                                                                                                                                                                        }
                                                                                                                                                                        .grand-total {
                                                                                                                                                                            font-size: 1.1em;
                                                                                                                                                                            font-weight: bold;
                                                                                                                                                                        }
                                                                                                                                                                        .footer-section {
                                                                                                                                                                            text-align: left;
                                                                                                                                                                            margin-top: 10px;
                                                                                                                                                                        }
                                                                                                                                                                        .footer-section p {
                                                                                                                                                                            margin: 0;
                                                                                                                                                                            line-height: 1.2;
                                                                                                                                                                        }
                                                                                                                                                                        hr {
                                                                                                                                                                            display: block;
                                                                                                                                                                            height: 1px;
                                                                                                                                                                            background: transparent;
                                                                                                                                                                            width: 100%;
                                                                                                                                                                            border: none;
                                                                                                                                                                            border-top: solid 2px #000 !important;
                                                                                                                                                                        }
                                                                                                                                                                    </style>
                                                                                                                                                                </head>
                                                                                                                                                                <body>
                                                                                                                                                                    <div class="receipt-container">
                                                                                                                                                                        ${salesContent}
                                                                                                                                                                    </div>
                                                                                                                                                                </body>
                                                                                                                                                            </html>
                                                                                                                                                        `);

                            printWindow.document.close();
                            printWindow.focus();

                            // Give it time to render styles, then print
                            setTimeout(() => {
                                printWindow.print();

                                // Auto-close after 10 seconds
                                setTimeout(() => {
                                    if (!printWindow.closed) {
                                        printWindow.close();
                                    }
                                    if (typeof onCompleteCallback === 'function') {
                                        onCompleteCallback();
                                    }
                                }, 00000);
                            }, 500);
                        }




                        // New event listener for page refresh or window close
                        document.addEventListener('DOMContentLoaded', function () {
                            let isRefresh = false;

                            // We'll set a flag if the user presses F5 or Ctrl+R (common refresh shortcuts)
                            window.addEventListener('keydown', function (e) {
                                if (e.key === 'F5' || (e.ctrlKey && e.key === 'r')) {
                                    isRefresh = true;
                                }
                            });

                            // The 'beforeunload' event fires for both refresh and closing.

                        });

                       // Store the PHP data in JavaScript variables for easier access
// This block initializes the global arrays on page load.
window.allSalesData = @json($sales->toArray()) || [];
window.printedSalesData = @json($salesPrinted->toArray()) || [];
window.unprintedSalesData = @json($salesNotPrinted->toArray()) || [];

// Global variable for currently displayed sales
window.currentDisplayedSalesData = [];

// Function to populate table
window.populateMainSalesTable = function(salesArray) {
    console.log("Entering populateMainSalesTable. Sales array received:", salesArray);

    // Keep a global snapshot of what’s shown
    window.currentDisplayedSalesData = JSON.parse(JSON.stringify(salesArray));

    const mainSalesTableBodyElement = document.getElementById('mainSalesTableBody');
    if (!mainSalesTableBodyElement) {
        console.error("Error: tbody with ID 'mainSalesTableBody' not found!");
        return;
    }

    mainSalesTableBodyElement.innerHTML = '';

    let totalSalesValue = 0;
    let totalPackCostValue = 0;

    if (!salesArray || salesArray.length === 0) {
        mainSalesTableBodyElement.innerHTML = '<tr><td colspan="8" class="text-center">No sales records found.</td></tr>';
        document.getElementById('mainTotalSalesValue').textContent = '0.00';
        document.getElementById('mainTotalSalesValueBottom').textContent = '0.00';
        document.getElementById('itemSummary').innerHTML = '';
        window.globalTotalPackCostValue = 0;
        return;
    }

    salesArray.forEach(sale => {
        const code = sale.code || 'N/A';
        const itemName = sale.item_name || 'N/A';
        const weight = sale.weight ? parseFloat(sale.weight).toFixed(2) : '0.00';
        const pricePerKg = sale.price_per_kg ? parseFloat(sale.price_per_kg).toFixed(2) : '0.00';
        const packs = sale.packs ? parseInt(sale.packs) : 0;
        const total = sale.total ? parseFloat(sale.total).toFixed(2) : (parseFloat(weight) * parseFloat(pricePerKg)).toFixed(2);
        const packDue = sale.pack_due ? parseFloat(sale.pack_due) : 0;
        const packCostValue = packs * packDue;

        const newRow = document.createElement('tr');
        newRow.dataset.saleId = sale.id;
        newRow.dataset.id = sale.id;
        newRow.dataset.customerCode = sale.customer_code;
        newRow.dataset.customerName = sale.customer_name;
        newRow.dataset.packDue = packDue; // Store pack_due in dataset for this row

        newRow.innerHTML = `
            <td data-field="code">${code}</td>
            <td data-field="item_name">${itemName}</td>
            <td data-field="weight">${weight}</td>
            <td data-field="price_per_kg">${pricePerKg}</td>
            <td data-field="total">${total}</td>
            <td data-field="packs">${packs}</td>
            <td data-field="pack_cost_value" style="display:none;">${packCostValue.toFixed(2)}</td>
        `;

        mainSalesTableBodyElement.appendChild(newRow);

        totalSalesValue += parseFloat(total);
        totalPackCostValue += packCostValue;
    });

    window.globalTotalPackCostValue = totalPackCostValue;

    const combinedTotal = totalSalesValue + totalPackCostValue;
    document.getElementById('mainTotalSalesValue').textContent = combinedTotal.toFixed(2);
    document.getElementById('mainTotalSalesValueBottom').textContent = combinedTotal.toFixed(2);

    // Build item summary
    const itemGroups = {};
    document.querySelectorAll('#mainSalesTableBody tr').forEach(row => {
        const itemName = row.querySelector('td[data-field="item_name"]').textContent.trim() || '';
        const weight = parseFloat(row.querySelector('td[data-field="weight"]').textContent) || 0;
        const packs = parseInt(row.querySelector('td[data-field="packs"]').textContent) || 0;
        const packCostValue = parseFloat(row.querySelector('td[data-field="pack_cost_value"]').textContent) || 0;

        if (!itemGroups[itemName]) {
            itemGroups[itemName] = { totalWeight: 0, totalPacks: 0, totalPackCost: 0 };
        }

        itemGroups[itemName].totalWeight += weight;
        itemGroups[itemName].totalPacks += packs;
        itemGroups[itemName].totalPackCost += packCostValue;
    });

    let summaryHtml = '<div style="font-size: 0.9rem; margin-top: 10px; display: flex; flex-wrap: wrap; gap: 1rem;">';
    for (const [itemName, totals] of Object.entries(itemGroups)) {
        summaryHtml += `
            <div style="padding: 0.25rem 0.5rem; border-radius: 0.5rem; background-color: #f3f4f6; box-shadow: 0 1px 2px 0 rgba(0, 0, 0, 0.05); font-size: 0.8rem;">
                <strong>${itemName}</strong>: බර (kg) = ${totals.totalWeight.toFixed(2)}, මලු = ${totals.totalPacks}
            </div>
        `;
    }
    summaryHtml += '</div>';

    const itemSummaryElement = document.getElementById('itemSummary');
    if (itemSummaryElement) {
        itemSummaryElement.innerHTML = summaryHtml;
    }

    console.log("populateMainSalesTable finished. Total sales:", totalSalesValue.toFixed(2), "Total pack cost:", totalPackCostValue.toFixed(2), "Combined total:", combinedTotal.toFixed(2));
};

// Call initially with all data
window.populateMainSalesTable(window.allSalesData);


    // ================= REMAINING STOCK CALCULATIONS =================
    let originalGrnPacks = 0;
    let originalGrnWeight = 0;
    let initialSalePacks = 0;
    let initialSaleWeight = 0;

    const remainingPacksDisplay = document.getElementById('remaining_packs_display');
    const remainingWeightDisplay = document.getElementById('remaining_weight_display');
    const packsField = document.getElementById('packs');
    const weightField = document.getElementById('weight');

    function updateRemainingStock() {
        if (updateSalesEntryBtn.style.display === 'inline-block') {
            const currentPacks = parseInt(packsField.value) || 0;
            const currentWeight = parseFloat(weightField.value) || 0;

            const packsDifference = currentPacks - initialSalePacks;
            const weightDifference = currentWeight - initialSaleWeight;

            const finalRemainingPacks = originalGrnPacks - packsDifference;
            const finalRemainingWeight = originalGrnWeight - weightDifference;

            remainingPacksDisplay.textContent = `BP: ${finalRemainingPacks}`;
            remainingWeightDisplay.textContent = `BW: ${finalRemainingWeight.toFixed(2)}`;
        }
    }

    packsField.addEventListener('input', updateRemainingStock);
    weightField.addEventListener('input', updateRemainingStock);


    // ================= POPULATE FORM FOR EDIT =================
 // ================= POPULATE FORM FOR EDIT =================
function populateFormForEdit(sale) {
    console.log("Populating form for sale:", sale);

    saleIdField.value = sale.id;
    newCustomerCodeField.value = sale.customer_code || '';
    customerNameField.value = sale.customer_name || '';
    newCustomerCodeField.readOnly = true;

    const grnDisplay = document.getElementById('grn_display');
    const grnSelect = document.getElementById('grn_select');

    grnDisplay.style.display = 'block';
    grnDisplay.value = sale.code || '';

    $(grnSelect).next('.select2-container').hide();

    const grnOption = $('#grn_select option').filter(function () {
        return $(this).val() === sale.code && $(this).data('supplierCode') === sale.supplier_code &&
            $(this).data('itemCode') === sale.item_code;
    });

    if (grnOption.length) {
        $('#grn_select').val(grnOption.val());
    } else {
        $('#grn_select').val(null);
    }

    // This is the key part:
    // We only fetch data initially and populate the fields once.
    if (sale.code) {
        fetch(`https://talentconnect.lk/AA/sms/api/grn-entry/${sale.code}`)
            .then(response => response.json())
            .then(grnData => {
                originalGrnPacks = parseInt(grnData.packs || 0);
                originalGrnWeight = parseFloat(grnData.weight || 0);

                initialSalePacks = parseInt(sale.packs || 0);
                initialSaleWeight = parseFloat(sale.weight || 0);

                weightField.value = initialSaleWeight.toFixed(2);
                weightField.select();

                packsField.value = initialSalePacks;

                pricePerKgField.value = parseFloat(sale.price_per_kg || 0).toFixed(2);
                calculateTotal();

                updateRemainingStock();
            })
            .catch(error => {
                console.error('Error fetching data:', error);
                remainingPacksDisplay.textContent = 'Remaining Packs: N/A';
                remainingWeightDisplay.textContent = 'Remaining: N/A kg';
            });
    } else {
        pricePerKgField.value = parseFloat(sale.price_per_kg || 0).toFixed(2);
        calculateTotal();
    }

    supplierSelect.value = sale.supplier_code || '';
    supplierDisplaySelect.value = sale.supplier_code || '';
    itemSelect.value = sale.item_code || '';
    itemSelect.dispatchEvent(new Event('change'));

    itemNameDisplayFromGrn.value = sale.item_name || '';
    itemNameField.value = sale.item_name || '';

    salesEntryForm.action = `sales/update/${sale.id}`;

    addSalesEntryBtn.style.display = 'none';
    updateSalesEntryBtn.style.display = 'inline-block';
    deleteSalesEntryBtn.style.display = 'inline-block';
    cancelEntryBtn.style.display = 'inline-block';

    weightField.focus();
    weightField.select();
}


    // ================= RESET FORM =================
    function resetForm() {
        console.log("Resetting form...");
        salesEntryForm.reset();
        saleIdField.value = '';
        newCustomerCodeField.readOnly = false;
        $('#customer_code_select').val(null).trigger('change.select2');
        $('#grn_select').val(null).trigger('change.select2');
        grnDisplay.value = 'Select GRN Entry...';
        supplierSelect.value = '';
        supplierDisplaySelect.value = '';
        itemSelect.value = '';
        itemSelect.dispatchEvent(new Event('change'));
        itemNameDisplayFromGrn.value = '';
        itemNameField.value = '';
        calculateTotal();

        salesEntryForm.action = "{{ route('grn.store') }}";

        addSalesEntryBtn.style.display = 'inline-block';
        updateSalesEntryBtn.style.display = 'none';
        deleteSalesEntryBtn.style.display = 'none';
        cancelEntryBtn.style.display = 'none';

        newCustomerCodeField.focus();
    }


    // ================= TABLE CLICK HANDLER =================
    document.getElementById('mainSalesTableBody').addEventListener('click', function (event) {
        const clickedRow = event.target.closest('tr[data-sale-id]');
        if (clickedRow) {
            const saleId = clickedRow.dataset.saleId;
            const saleToEdit = currentDisplayedSalesData.find(sale => String(sale.id) === String(saleId));
            if (saleToEdit) {
                populateFormForEdit(saleToEdit);
            }
        }
    });


    // ================= ENTER KEY NAVIGATION =================
    document.getElementById('weight').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('price_per_kg').focus();
            document.getElementById('price_per_kg').select();
        }
    });

    document.getElementById('price_per_kg').addEventListener('keydown', function (e) {
        if (e.key === 'Enter') {
            e.preventDefault();
            document.getElementById('packs').focus();
            document.getElementById('packs').select();
        }
    });


    // ================= FETCH UNPRINTED SALES =================
    $(document).ready(function () {
        function debounce(func, delay) {
            let timeout;
            return function (...args) {
                const context = this;
                clearTimeout(timeout);
                timeout = setTimeout(() => func.apply(context, args), delay);
            };
        }

        function fetchUnprintedSales(customerCode) {
            let tableBody = $('#mainSalesTableBody');
            tableBody.empty();
            $('#customer_name').val('');
            $('#mainTotalSalesValue').text("0.00");
            $('#mainTotalSalesValueBottom').text("0.00");

            if (customerCode) {
                $.ajax({
                     url: 'https://talentconnect.lk/AA/sms/api/sales/unprinted/' + customerCode,

                    method: 'GET',
                    success: function (response) {
                        if (response.length > 0) {
                            populateMainSalesTable(response);
                          
                        } else {
                            tableBody.html('<tr><td colspan="7" class="text-center"></td></tr>');
                        }
                    },
                    error: function (xhr) {
                        console.error("AJAX Error fetching sales records:", xhr.responseText);
                        tableBody.html('<tr><td colspan="7" class="text-center text-danger">Error fetching sales data. Please try again.</td></tr>');
                    }
                });
            } else {
                tableBody.html('<tr><td colspan="7" class="text-center">Please enter a customer code to view records.</td></tr>');
            }
        }

        const debouncedFetchUnprintedSales = debounce(fetchUnprintedSales, 300);
        $('#new_customer_code').on('keyup', function () {
            let customerCode = $(this).val().trim();
            debouncedFetchUnprintedSales(customerCode);
        });
    });


                        function resetForm() {
                            console.log("Resetting form...");
                            salesEntryForm.reset();
                            saleIdField.value = '';
                            newCustomerCodeField.readOnly = false;
                            $('#customer_code_select').val(null).trigger('change.select2');
                            $('#grn_select').val(null).trigger('change.select2');
                            grnDisplay.value = 'Select GRN Entry...';
                            supplierSelect.value = '';
                            supplierDisplaySelect.value = '';
                            itemSelect.value = '';
                            itemSelect.dispatchEvent(new Event('change'));
                            itemNameDisplayFromGrn.value = '';
                            itemNameField.value = '';
                            calculateTotal();
                            

                            salesEntryForm.action = "{{ route('grn.store') }}";

                            addSalesEntryBtn.style.display = 'inline-block';
                            updateSalesEntryBtn.style.display = 'none';
                            deleteSalesEntryBtn.style.display = 'none';
                            cancelEntryBtn.style.display = 'none';

                            newCustomerCodeField.focus();
                            console.log("Form reset complete.");
                        }

                        document.getElementById('mainSalesTableBody').addEventListener('click', function (event) {
                            const clickedRow = event.target.closest('tr[data-sale-id]');
                            if (clickedRow) {
                                const saleId = clickedRow.dataset.saleId;
                                console.log("Row clicked, sale ID:", saleId);
                                const saleToEdit = currentDisplayedSalesData.find(sale => String(sale.id) === String(saleId));
                                if (saleToEdit) {
                                    console.log("Sale found in currentDisplayedSalesData for ID:", saleId, saleToEdit);
                                    populateFormForEdit(saleToEdit);
                                } else {
                                    console.warn("Sale NOT found in currentDisplayedSalesData for ID:", saleId);
                                    alert(
                                        "Could not find this record for editing. It might not be in the currently displayed sales list. Please try reloading the page if this persists."
                                    );
                                }
                            }
                        });

                        // Get references
                        const salesEntryForm = document.getElementById('salesEntryForm');
                        const updateSalesEntryBtn = document.getElementById('updateSalesEntryBtn');
                        const saleIdField = document.getElementById('sale_id');

                        let originalFormData = {}; // To store the original values for comparison

                        // Helper function to get current form data as an object
                        function getCurrentFormData(form) {
                            const formData = new FormData(form);
                            const data = {};
                            formData.forEach((value, key) => {
                                data[key] = value;
                            });
                            return data;
                        }

                        // Store original form data when a record is selected (you must call this manually when loading data)
                        function storeOriginalFormData() {
                            if (salesEntryForm) {
                                originalFormData = getCurrentFormData(salesEntryForm);
                            }
                        }

                        // Compare current data with original to see if any changes were made
                        function isFormDataChanged(currentData) {
                            for (let key in currentData) {
                                if (currentData[key] !== originalFormData[key]) {
                                    return true; // At least one field has changed
                                }
                            }
                            return false;
                        }

                        if (salesEntryForm && updateSalesEntryBtn && saleIdField) {

                            // Enter keypress triggers update only if update button is visible
                            salesEntryForm.addEventListener('keypress', function (event) {
                                if (event.key === 'Enter') {
                                    const style = window.getComputedStyle(updateSalesEntryBtn);
                                    const visible = style.display !== 'none' && style.visibility !== 'hidden';
                                    const rendered = updateSalesEntryBtn.offsetWidth > 0 || updateSalesEntryBtn.offsetHeight > 0;

                                    if (visible && rendered) {
                                        event.preventDefault();
                                        updateSalesEntryBtn.click();
                                    }
                                }
                            });

                              // Click event for update button
                       updateSalesEntryBtn.addEventListener('click', function () {
    const saleId = saleIdField.value;
    if (!saleId) {
        alert('No record selected for update.');
        return;
    }

    let currentFormData = getCurrentFormData(salesEntryForm);

    // For updates, get the NEW values from the form fields
    if (updateSalesEntryBtn.style.display === 'inline-block') {
        // Get the NEW GRN code from the grn_display field (not the disabled select)
        const newGrnCode = grnDisplay.value.split('|')[0].trim(); // Extract code from display format

        // Update form data with NEW values from the form
        currentFormData['code'] = newGrnCode || '';
        currentFormData['grn_entry_code'] = newGrnCode || '';

        // Get other values from the form fields
        currentFormData['item_name'] = itemNameDisplayFromGrn.value || '';
        currentFormData['item_code'] = document.querySelector('input[name="item_code"]').value || '';
        currentFormData['supplier_code'] = supplierSelect.value || '';

        // Remove the grn_select field from the form data since it's disabled
        delete currentFormData['grn_select'];
    }

    if (!isFormDataChanged(currentFormData)) {
        alert('No changes detected. Update not required.');
        return;
    }

    // Add method and token
    currentFormData['_method'] = 'PUT';
    currentFormData['_token'] = '{{ csrf_token() }}';

    fetch(`https://talentconnect.lk/AA/sms/sales/update/${saleId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(currentFormData)
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => Promise.reject(errorData));
        }
        return response.json();
    })
    .then(result => {
        console.log("Server response:", result);
        console.log("Updated sale record received from server:", result.sale);

        if (result.success && result.sale) {
            const updatedSale = JSON.parse(JSON.stringify(result.sale));

            // 1️⃣ Update currentDisplayedSalesData
            const updatedIndex = currentDisplayedSalesData.findIndex(
                sale => String(sale.id) === String(saleId)
            );
            if (updatedIndex !== -1) {
                currentDisplayedSalesData[updatedIndex] = updatedSale;
            }

            // 2️⃣ Update allSalesData
            const allIndex = allSalesData.findIndex(
                sale => String(sale.id) === String(saleId)
            );
            if (allIndex !== -1) {
                allSalesData[allIndex] = updatedSale;
            }

            // 3️⃣ Update printed/unprinted arrays
            // First remove old record
            for (const arr of [printedSalesData, unprintedSalesData]) {
                if (Array.isArray(arr)) {
                    const idx = arr.findIndex(sale => String(sale.id) === String(saleId));
                    if (idx !== -1) arr.splice(idx, 1);
                } else {
                    // If keyed by customer_code
                    for (const key in arr) {
                        const idx = arr[key].findIndex(sale => String(sale.id) === String(saleId));
                        if (idx !== -1) arr[key].splice(idx, 1);
                    }
                }
            }

            // Then push updated one into correct place
            if (updatedSale.bill_printed) {
                if (Array.isArray(printedSalesData)) {
                    printedSalesData.push(updatedSale);
                } else {
                    if (!printedSalesData[updatedSale.customer_code]) {
                        printedSalesData[updatedSale.customer_code] = [];
                    }
                    printedSalesData[updatedSale.customer_code].push(updatedSale);
                }
            } else {
                if (Array.isArray(unprintedSalesData)) {
                    unprintedSalesData.push(updatedSale);
                } else {
                    if (!unprintedSalesData[updatedSale.customer_code]) {
                        unprintedSalesData[updatedSale.customer_code] = [];
                    }
                    unprintedSalesData[updatedSale.customer_code].push(updatedSale);
                }
            }

            // 4️⃣ Re-render the table
            populateMainSalesTable(currentDisplayedSalesData);

            // 5️⃣ Reset form + preserve customer code
            document.getElementById('remaining_packs_display').innerText = "BP: 0";
            document.getElementById('remaining_weight_display').innerText = "BW: 0.00 kg";
            const preservedCustomerCode = newCustomerCodeField.value;
            resetForm();
            newCustomerCodeField.value = preservedCustomerCode;

            console.log("✅ Sale record successfully updated in ALL local arrays");
        } else {
            alert('Update failed: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error updating sales entry:', error);
        let errorMessage = 'An error occurred during update.';
        if (error?.message) errorMessage += '\n' + error.message;
        if (error?.errors) {
            for (const key in error.errors) {
                errorMessage += `\n${key}: ${error.errors[key].join(', ')}`;
            }
        }
        alert(errorMessage);
    });
});
}
      deleteSalesEntryBtn.addEventListener('click', function () {
    const saleId = saleIdField.value;
    if (!saleId) {
        alert('No record selected for deletion.');
        return;
    }

    if (!confirm('Are you sure you want to delete this sales record?')) {
        return;
    }

   fetch(`https://talentconnect.lk/AA/sms/sales/delete/${saleId}`, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({
            _method: 'DELETE',
            _token: '{{ csrf_token() }}'
        })
    })
    .then(response => {
        if (!response.ok) {
            return response.json().then(errorData => Promise.reject(errorData));
        }
        return response.json();
    })
    .then(result => {
        if (result.success) {
          

            // ✅ Remove row from the table
            const rowToDelete = document.querySelector(`#mainSalesTableBody tr[data-id="${saleId}"]`);
            if (rowToDelete) {
                rowToDelete.remove();
                 currentDisplayedSalesData = currentDisplayedSalesData.filter(sale => String(sale.id) !== String(saleId));
            }

            // ✅ Recalculate totals
            let totalSum = 0;
            document.querySelectorAll('#mainSalesTableBody tr').forEach(row => {
                const totalCell = row.querySelector('td:nth-child(5)'); // adjust column index if needed
                if (totalCell) {
                    totalSum += parseFloat(totalCell.textContent) || 0;
                }
            });

            // Update total fields
            document.getElementById('mainTotalSalesValue').textContent = totalSum.toFixed(2);
            document.getElementById('mainTotalSalesValueBottom').textContent = totalSum.toFixed(2);

           

            // 🔹 Reset BP and BW displays
            document.getElementById('remaining_packs_display').innerText = "BP: 0";
            document.getElementById('remaining_weight_display').innerText = "BW: 0.00 kg";
              const preservedCustomerCode = newCustomerCodeField.value;
             resetForm();
               newCustomerCodeField.value = preservedCustomerCode;

            console.log("Form reset complete after deletion.");
        } else {
            alert('Delete failed: ' + result.message);
        }
    })
    .catch(error => {
        console.error('Error deleting sales entry:', error);
        let errorMessage = 'An error occurred during deletion.';
        if (error && error.message) {
            errorMessage += '\n' + error.message;
        }
        alert(errorMessage);
    });
});


                        cancelEntryBtn.addEventListener('click', resetForm);

                        resetForm();

                        $('.customer-header').on('click', function () {
                            console.log("Customer header clicked!");

                            const customerCode = $(this).data('customer-code');
                            const billType = $(this).data('bill-type');
                            const billNo = $(this).data('bill-no'); // This will now correctly have a value or ''

                            console.log("Clicked Customer Code:", customerCode);
                            console.log("Clicked Bill Type:", billType);
                            console.log("Clicked Bill No:", billNo);
                            newCustomerCodeField.value = customerCode;

                            let salesToDisplay = [];

                            if (billType === 'printed') {
                                console.log("Attempting to filter PRINTED sales...");
                                if (printedSalesData[customerCode] && Array.isArray(printedSalesData[customerCode])) {
                                    salesToDisplay = printedSalesData[customerCode].filter(sale => {
                                        // Ensure both are treated as strings for comparison
                                        return String(sale.bill_no) === String(billNo);
                                    });
                                    console.log("Printed sales data for customerCode:", printedSalesData[customerCode]);
                                } else {
                                    console.log("No printed sales data found or not an array for customerCode:",
                                        customerCode);
                                }
                            } else if (billType === 'unprinted') {
                                console.log("Attempting to filter UNPRINTED sales...");
                                if (unprintedSalesData[customerCode] && Array.isArray(unprintedSalesData[customerCode])) {
                                    salesToDisplay = unprintedSalesData[customerCode];
                                    console.log("Unprinted sales data for customerCode:", unprintedSalesData[
                                        customerCode]);
                                } else {
                                    console.log("No unprinted sales data found or not an array for customerCode:",
                                        customerCode);
                                }
                            } else {
                                console.log("Unknown billType:", billType);
                            }

                            console.log("Sales to Display after filter:", salesToDisplay);
                            populateMainSalesTable(salesToDisplay);
                        });


                        $(document).on('click', '.print-bill-btn', function () {
                            var customerCode = $(this).data('customer-code');
                            if (confirm('Are you sure you want to print the bill for ' + customerCode +
                                '? This will mark all *unprinted* sales for this customer as printed and processed.')) {
                                $.ajax({
                                    url: '/sales/print-bill/' + customerCode,
                                    type: 'POST',
                                    data: {
                                        _token: '{{ csrf_token() }}',
                                        customer_code: customerCode
                                    },
                                    success: function (response) {
                                        if (response.success) {
                                            alert(response.message);
                                            sessionStorage.setItem('focusOnCustomerSelect', 'true');
                                            location.reload();
                                        } else {
                                            alert('Error: ' + response.message);
                                        }
                                    },
                                    error: function (xhr) {
                                        console.error("AJAX error:", xhr.responseText);
                                        alert('An error occurred while trying to print the bill.');
                                    }
                                });
                            }
                        });

                        if (sessionStorage.getItem('focusOnCustomerSelect') === 'true') {
                            $(document).on('select2:open', function () {
                                document.querySelector('.select2-search__field').focus();
                            });
                            // Check if the element actually exists and is a select2 element
                            if ($('#new_customer_code').data('select2')) {
                                $('#new_customer_code').select2('open');
                            } else {
                                // Fallback to focus the customer code text input if select2 not applied or is hidden
                                newCustomerCodeField.focus();
                            }
                            sessionStorage.removeItem('focusOnCustomerSelect');
                        }
                    });
                </script>
                {{-- typing customer code and fetching data from unprinted sales records them) --}}



@endsection