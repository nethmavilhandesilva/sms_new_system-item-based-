@extends('layouts.app')

@section('content')
<style>
    body { background-color: #99ff99; }
    .bg-light { background-color: #ffffff !important; }
    .list-group-item-action { cursor: pointer; }
    /* Style for the transaction history modal link */
    .view-history-link { cursor: pointer; color: #198754; font-weight: bold; }
    .view-history-link:hover { text-decoration: underline; }
    .text-uppercase {
    text-transform: uppercase;
}
    /* Styles for the new "Many Payments" form */
    #grnSelectionArea {
        max-height: 300px;
        overflow-y: auto;
        border: 1px solid #ddd;
        padding: 10px;
        border-radius: 5px;
        background-color: #f8f9fa;
    }
    #grnSelectionArea .form-check {
        padding: 5px;
        border-bottom: 1px solid #eee;
    }
    #grnSelectionArea .form-check:last-child {
        border-bottom: none;
    }
    /* Style for the allocation amount display */
    .allocated-amount {
        font-weight: bold;
    }
</style>

<div class="container mt-4">
    <h3 class="text-success mb-3">Supplier Management</h3>

    {{-- Alerts --}}
    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if (session('error'))
        <div class="alert alert-danger">{{ session('error') }}</div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
        </div>
    @endif

    {{-- Radio buttons --}}
    <div class="mb-3">
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="action_type" id="addSupplierRadio" checked>
            <label class="form-check-label fw-bold" for="addSupplierRadio">Purchase from Supplier</label>
        </div>
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="action_type" id="paymentRadio">
            <label class="form-check-label fw-bold" for="paymentRadio">Settle to Supplier</label>
        </div>
        {{-- NEW "Many Payments" Radio Button --}}
        <div class="form-check form-check-inline">
            <input class="form-check-input" type="radio" name="action_type" id="manyPaymentsRadio">
            <label class="form-check-label fw-bold" for="manyPaymentsRadio">Many Payments</label>
        </div>
    </div>

    {{-- Purchase Form --}}
    <form id="supplierForm" action="{{ route('suppliers2.store') }}" method="POST" class="border p-3 rounded bg-light">
        @csrf
        <input type="hidden" name="transaction_id" id="transaction_id"> {{-- For inline edit --}}
        <div class="row mb-3 align-items-end">
            <div class="col-md-3 position-relative">
                <label for="supplier_search" class="form-label">Select Supplier</label>
               <input type="text" id="supplier_search" class="form-control text-uppercase" placeholder="🔍 Type supplier code..." autocomplete="off" required>
                <input type="hidden" name="existing_supplier_id" id="supplier_id">
                <input type="hidden" name="supplier_code" id="supplier_code">
                <input type="hidden" name="supplier_name" id="supplier_name">

                <div id="supplierDropdown" class="list-group position-absolute w-100 shadow-sm"
                     style="z-index:10; display:none; max-height:200px; overflow-y:auto;">
                    @foreach($existingSuppliersWithBalance as $ex)
                        <button type="button" class="list-group-item list-group-item-action"
                                data-id="{{ $ex->id }}" data-code="{{ $ex->code }}" data-name="{{ $ex->name }}" data-balance="{{ number_format($ex->balance, 2) }}">
                            {{ $ex->code }}{{ $ex->name ? ' — ' . $ex->name : '' }} (Bal: {{ number_format($ex->balance, 2) }})
                        </button>
                    @endforeach
                </div>
                <div id="purchaseBalanceDisplay" class="mt-2 fw-bold" style="display:none;">
                    Current Balance: <span id="current_supplier_balance">0.00</span>
                </div>
            </div>

            <div class="col-md-3">
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" class="form-control" rows="1" placeholder="Enter description..."></textarea>
            </div>

            <div class="col-md-3">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" name="total_amount" id="total_amount" class="form-control" step="0.01" required>
            </div>

            <div class="col-md-3 position-relative">
                <label for="grn_search" class="form-label">Search GRN</label>
                <input type="text" id="grn_search" class="form-control" placeholder="🔍 Type GRN code or item name..." autocomplete="off">
                <input type="hidden" name="grn_id" id="grn_id" required> 

                <div id="grnDropdown" class="list-group position-absolute w-100 shadow-sm"
                     style="z-index:10; display:none; max-height:200px; overflow-y:auto;">
                    @foreach($grnOptions as $id => $grnNo)
                        <button type="button" class="list-group-item list-group-item-action"
                                data-id="{{ $id }}" data-name="{{ $grnNo }}">{{ $grnNo }}</button>
                    @endforeach
                </div>
            </div>
        </div>

        <button type="submit" class="btn btn-success" id="submitButton">Save Purchase</button>
        <button type="button" class="btn btn-secondary" id="cancelEditButton" style="display:none;">Cancel Edit</button>
    </form>

    {{-- Payment Form --}}
    {{-- *** IMPORTANT: Added enctype for file uploads *** --}}
    <form id="paymentForm" action="{{ route('suppliers2.payment') }}" method="POST" 
      class="border p-3 rounded bg-light" style="display:none;" enctype="multipart/form-data">
    @csrf

    <div class="row g-3 align-items-end">
        {{-- Supplier --}}
        <div class="col-md-3 position-relative">
            <label for="payment_supplier_search" class="form-label">Select Supplier</label>
            <input type="text" id="payment_supplier_search" class="form-control" placeholder="🔍 Type supplier code..." autocomplete="off" required>
            <input type="hidden" name="supplier_code" id="payment_supplier_code">

            <div id="paymentSupplierDropdown" class="list-group position-absolute w-100 shadow-sm"
                 style="z-index:10; display:none; max-height:200px; overflow-y:auto;">
                @foreach($existingSuppliersWithBalance as $supplier)
                    {{-- *** UPDATED: Added data-account *** --}}
                    <button type="button" class="list-group-item list-group-item-action"
                            data-code="{{ $supplier->code }}" data-name="{{ $supplier->name }}" data-balance="{{ number_format($supplier->balance, 2) }}"
                            data-account="{{ $supplier->account_no ?? '' }}">
                        {{ $supplier->code }} — {{ $supplier->name }} (Bal: {{ number_format($supplier->balance, 2) }})
                    </button>
                @endforeach
            </div>
            <div id="paymentBalanceDisplay" class="mt-2 fw-bold" style="display:none;">
                Current Balance: <span id="current_payment_balance">0.00</span>
            </div>
        </div>

        {{-- Description --}}
        <div class="col-md-3">
            <label for="payment_description" class="form-label">Description</label>
            <textarea name="description" id="payment_description" class="form-control" rows="1" placeholder="Enter payment details..."></textarea>
        </div>

        {{-- GRN Search --}}
        <div class="col-md-3 position-relative">
            <label for="payment_grn_search" class="form-label">Search GRN (Optional)</label>
            <input type="text" id="payment_grn_search" class="form-control" placeholder="🔍 Type GRN code or item name..." autocomplete="off">
            <input type="hidden" name="grn_id" id="payment_grn_id">

            <div id="paymentGrnDropdown" class="list-group position-absolute w-100 shadow-sm"
                 style="z-index:10; display:none; max-height:200px; overflow-y:auto;">
                @foreach($grnOptions as $id => $grnNo)
                    <button type="button" class="list-group-item list-group-item-action"
                            data-id="{{ $id }}" data-name="{{ $grnNo }}">{{ $grnNo }}</button>
                @endforeach
            </div>
        </div>

        {{-- Payment Amount --}}
        <div class="col-md-3">
            <label for="payment_amount" class="form-label">Payment Amount</label>
            <input type="number" name="payment_amount" id="payment_amount" class="form-control" step="0.01" required>
        </div>
    </div>

    {{-- Payment Method --}}
    <div class="row g-3 mt-2">
        <div class="col-md-3">
            <label class="form-label fw-bold">Payment Method</label>
            <div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_cash" value="cash" checked>
                    <label class="form-check-label" for="payment_method_cash">Cash</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_cheque" value="cheque">
                    <label class="form-check-label" for="payment_method_cheque">Cheque</label>
                </div>
                {{-- NEW: Bank Deposit Radio --}}
                <div class="form-check form-check-inline">
                    <input class="form-check-input" type="radio" name="payment_method" id="payment_method_bank" value="bank_deposit">
                    <label class="form-check-label" for="payment_method_bank">Bank Deposit</label>
                </div>
            </div>
        </div>

        {{-- Cheque Details (Hidden by default) --}}
        <div class="col-md-9" id="payment_cheque_details" style="display:none;">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="payment_cheque_no" class="form-label">Cheque No</label>
                    <input type="text" name="payment_cheque_no" id="payment_cheque_no" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="payment_cheque_date" class="form-label">Cheque Date</label>
                    <input type="date" name="payment_cheque_date" id="payment_cheque_date" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="payment_bank_name" class="form-label">Bank Name</label>
                    <input type="text" name="payment_bank_name" id="payment_bank_name" class="form-control">
                </div>
            </div>
        </div>
        
        {{-- NEW: Bank Deposit Details (Hidden by default) --}}
        <div class="col-md-9" id="payment_bank_deposit_details" style="display:none;">
            <div class="row g-3">
                <div class="col-md-3">
                    <label for="payment_bank_name_deposit" class="form-label">Bank Name</label>
                    <input type="text" name="payment_bank_name_deposit" id="payment_bank_name_deposit" class="form-control">
                </div>
                <div class="col-md-3">
                    <label for="payment_account_no" class="form-label">Account No</label>
                    {{-- This is the target field --}}
                    <input type="text" name="payment_account_no" id="payment_account_no" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="payment_bank_slip" class="form-label">Bank Slip (Image)</label>
                    <input type="file" name="payment_bank_slip" id="payment_bank_slip" class="form-control" accept="image/*">
                </div>
            </div>
        </div>

    </div>

    <div class="mt-3 text-end">
        <button type="submit" class="btn btn-primary">Submit Payment</button>
    </div>
</form>

    {{-- NEW "Many Payments" Form --}}
    {{-- *** IMPORTANT: Added enctype for file uploads *** --}}
    <form id="manyPaymentsForm" action="{{ route('suppliers2.payment.many') }}" method="POST" 
          class="border p-3 rounded bg-light" style="display:none;" enctype="multipart/form-data">
        @csrf
        
        {{-- This container will hold our dynamically generated payment inputs --}}
        <div id="grnPaymentInputsContainer"></div>

        <div class="row">
            {{-- Supplier Selection --}}
            <div class="col-md-4 position-relative">
                <label for="many_supplier_search" class="form-label">Select Supplier</label>
                <input type="text" id="many_supplier_search" class="form-control" placeholder="🔍 Type supplier code..." autocomplete="off" required>
                <input type="hidden" name="supplier_code" id="many_supplier_code">

                <div id="manySupplierDropdown" class="list-group position-absolute w-100 shadow-sm"
                     style="z-index:10; display:none; max-height:200px; overflow-y:auto;">
                    @foreach($existingSuppliersWithBalance as $supplier)
                        {{-- *** UPDATED: Added data-account *** --}}
                        <button type="button" class="list-group-item list-group-item-action"
                                data-code="{{ $supplier->code }}" data-name="{{ $supplier->name }}" data-balance="{{ number_format($supplier->balance, 2) }}"
                                data-account="{{ $supplier->account_no ?? '' }}">
                            {{ $supplier->code }} — {{ $supplier->name }} (Bal: {{ number_format($supplier->balance, 2) }})
                        </button>
                    @endforeach
                </div>
                <div id="manyBalanceDisplay" class="mt-2 fw-bold" style="display:none;">
                    Current Balance: <span id="current_many_balance">0.00</span>
                </div>
            </div>

            {{-- Description --}}
            <div class="col-md-8">
                <label for="many_payment_description" class="form-label">Payment Description</label>
                <textarea name="description" id="many_payment_description" class="form-control" rows="1" 
                          placeholder="e.g., Bulk payment for selected GRNs"></textarea>
            </div>
        </div>

        {{-- Payment Amount Field --}}
        <div class="row mt-3">
            <div class="col-md-4">
                <label for="many_payment_amount" class="form-label fw-bold text-primary">Payment Amount</label>
                <input type="number" name="many_payment_amount" id="many_payment_amount" class="form-control" 
                       step="0.01" required placeholder="Enter total amount...">
            </div>
        </div>

        {{-- Payment Method (Many) --}}
        <div class="row g-3 mt-2">
            <div class="col-md-4">
                <label class="form-label fw-bold">Payment Method</label>
                <div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="many_payment_method" id="many_payment_method_cash" value="cash" checked>
                        <label class="form-check-label" for="many_payment_method_cash">Cash</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="many_payment_method" id="many_payment_method_cheque" value="cheque">
                        <label class="form-check-label" for="many_payment_method_cheque">Cheque</label>
                    </div>
                    {{-- NEW: Bank Deposit Radio --}}
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="many_payment_method" id="many_payment_method_bank" value="bank_deposit">
                        <label class="form-check-label" for="many_payment_method_bank">Bank Deposit</label>
                    </div>
                </div>
            </div>

            {{-- Cheque Details (Hidden by default) --}}
            <div class="col-md-8" id="many_cheque_details" style="display:none;">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="many_cheque_no" class="form-label">Cheque No</label>
                        <input type="text" name="many_cheque_no" id="many_cheque_no" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="many_cheque_date" class="form-label">Cheque Date</label>
                        <input type="date" name="many_cheque_date" id="many_cheque_date" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label for="many_bank_name" class="form-label">Bank Name</label>
                        <input type="text" name="many_bank_name" id="many_bank_name" class="form-control">
                    </div>
                </div>
            </div>

            {{-- NEW: Bank Deposit Details (Hidden by default) --}}
            <div class="col-md-8" id="many_bank_deposit_details" style="display:none;">
                <div class="row g-3">
                    <div class="col-md-3">
                        <label for="many_bank_name_deposit" class="form-label">Bank Name</label>
                        <input type="text" name="many_bank_name_deposit" id="many_bank_name_deposit" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label for="many_account_no" class="form-label">Account No</label>
                        {{-- This is the second target field --}}
                        <input type="text" name="many_account_no" id="many_account_no" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="many_bank_slip" class="form-label">Bank Slip (Image)</label>
                        <input type="file" name="many_bank_slip" id="many_bank_slip" class="form-control" accept="image/*">
                    </div>
                </div>
            </div>

        </div>
        
        {{-- GRN Selection Area (Populated by JS) --}}
        <div id="grnSelectionContainer" class="mt-3" style="display:none;">
            <label class="form-label fw-bold">Select Partially Paid GRNs to Pay (in order):</label>
            <div id="grnSelectionArea">
                {{-- Content will be loaded via AJAX --}}
                <p class="text-muted text-center">Select a supplier to see partially paid GRNs.</p>
            </div>
        </div>

        {{-- Total and Submit --}}
        <div class="row mt-3 align-items-center">
            <div class="col-md-8">
                <h5 id="totalSelectedDisplay" class="fw-bold text-success" style="display:none;">
                    Total Allocated: <span>0.00</span>
                </h5>
                <h5 id="paymentRemainingDisplay" class="fw-bold" style="display:none;">
                    Remaining: <span>0.00</span>
                </h5>
            </div>
            <div class="col-md-4 text-end">
                <button type="submit" class="btn btn-primary" id="manyPaymentSubmitButton" disabled>Make Payment for Selected</button>
            </div>
        </div>
    </form>


    <hr class="my-4">

    {{-- Supplier Records & Search Filter --}}
    <h5 class="text-success">Supplier Records (Transactions)</h5>
    <div class="mb-3">
       <input type="text" id="supplierTableSearch" class="form-control text-uppercase" placeholder="🔍 Filter by Supplier Code..." autocomplete="off">
    </div>

    <table class="table table-bordered table-striped table-hover mt-3">
        <thead class="table-success">
        <tr>
            <th>ID</th>
            <th>Date</th>
            <th>Supplier Code</th>
            <th>Supplier Name</th>
            <th class="text-end">Amount</th>
            <th>Description</th>
            <th>GRN No</th>
            <th>Actions</th>
        </tr>
        </thead>
        <tbody id="supplierTableBody">
        @forelse($suppliers as $supplier)
            <tr data-transaction="{{ $supplier->toJson() }}">
                <td>{{ $supplier->id }}</td>
                <td>{{ $supplier->date }}</td>
                <td><span class="view-history-link" data-supplier-code="{{ $supplier->supplier_code }}">{{ $supplier->supplier_code }}</span></td>
                <td>{{ $supplier->supplier_name }}</td>
                <td class="text-end {{ $supplier->total_amount < 0 ? 'text-danger' : 'text-success' }}">
                    {{ number_format(abs($supplier->total_amount), 2) }}
                </td>
                <td>{{ $supplier->description ?? '-' }}</td>
                <td>{{ $supplier->grn->code ?? '-' }}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-warning editButton">Edit</button>
                    <form action="{{ route('suppliers2.destroy', $supplier->id) }}" method="POST" class="d-inline">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger" onclick="return confirm('Delete this transaction?')">Delete</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="8" class="text-center text-muted">No transactions found.</td></tr>
        @endforelse
        </tbody>
    </table>
        <a href="{{ route('supplier.report') }}" class="btn btn-dark">
                    සියලු සැපයුම්කරුවන්ගේ වාර්තාව
                </a>
</div>

{{-- Transaction Modal --}}
<div class="modal fade" id="transactionModal" tabindex="-1" aria-labelledby="transactionModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content shadow-lg">
            
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title" id="transactionModalLabel">Supplier Transaction History</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>

            <div class="modal-body bg-light">
                
                <div class="border rounded p-3 mb-3 bg-white shadow-sm">
                    <h6 class="fw-bold mb-2">
                        Supplier: 
                        <span id="modal_supplier_code" class="text-success"></span> - 
                        <span id="modal_supplier_name"></span>
                    </h6>
                    <div class="row small text-muted">
                        <div class="col-md-4"><strong>📧 Email:</strong> <span id="modal_supplier_email">N/A</span></div>
                        <div class="col-md-4"><strong>📞 Phone:</strong> <span id="modal_supplier_phone">N/A</span></div>
                        <div class="col-md-4"><strong>🏠 Address:</strong> <span id="modal_supplier_address">N/A</span></div>
                    </div>
                </div>

                <div class="row g-3 mb-4 text-center">
                    <div class="col-md-4">
                        <div class="card border-success">
                            <div class="card-body p-2">
                                <h6 class="text-success mb-1">Total Purchases</h6>
                                <h5 id="modal_total_purchases" class="fw-bold">0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-danger">
                            <div class="card-body p-2">
                                <h6 class="text-danger mb-1">Total Payments</h6>
                                <h5 id="modal_total_payments" class="fw-bold">0.00</h5>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-4">
                        <div class="card border-secondary">
                            <div class="card-body p-2">
                                <h6 class="text-dark mb-1">Remaining Balance</h6>
                                <h5 id="modal_remaining_balance" class="fw-bold text-danger">0.00</h5>
                            </div>
                        </div>
                    </div>
                </div>

                <h6 class="text-success mb-2">All Transactions</h6>
                <div class="table-responsive border rounded bg-white shadow-sm" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-sm table-striped table-hover align-middle mb-0">
                    <thead class="table-light sticky-top" style="z-index: 1;">
                        <tr>
                            <th>Date</th>
                            <th>Type</th>
                            <th>Description</th>
                            <th>GRN</th>
                            <th class="text-end">Amount</th>
                            <th class="text-end">Balance</th>
                            <th>Attachment</th> {{-- NEW COLUMN --}}
                        </tr>
                    </thead>
                    <tbody id="transactionHistoryBody">
                        {{-- Populated dynamically --}}
                    </tbody>
                </table>
                </div>
                <h6 class="text-primary mb-2 mt-3">GRN Payment Breakdown</h6>
                <div class="table-responsive border rounded bg-white shadow-sm" style="max-height: 250px; overflow-y: auto;">
                    <table class="table table-sm table-striped table-hover align-middle mb-0">
                        <thead class="table-light sticky-top" style="z-index: 1;">
                            <tr>
                                <th>GRN No</th>
                                <th class="text-end">GRN Total</th>
                                <th class="text-end">Total Paid</th>
                                <th class="text-end">Remaining</th>
                                <th>Last Payment Date</th>
                            </tr>
                        </thead>
                        <tbody id="grnPaymentSummaryBody">
                            {{-- Populated dynamically --}}
                        </tbody>
                    </table>
                </div>

            </div>

            <div class="modal-footer bg-light border-top-0 pt-0">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>

        </div>
    </div>
</div>


<script>
// --- CSRF Token for AJAX ---
const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

// --- Form Toggles ---
const addRadio = document.getElementById('addSupplierRadio');
const payRadio = document.getElementById('paymentRadio');
const manyPayRadio = document.getElementById('manyPaymentsRadio');
const supplierForm = document.getElementById('supplierForm');
const paymentForm = document.getElementById('paymentForm');
const manyPaymentsForm = document.getElementById('manyPaymentsForm');

function toggleForms() {
    supplierForm.style.display = addRadio.checked ? 'block' : 'none';
    paymentForm.style.display = payRadio.checked ? 'block' : 'none';
    manyPaymentsForm.style.display = manyPayRadio.checked ? 'block' : 'none';

    updateDescriptionField(); // Update description on radio change

    // Set focus
    if (addRadio.checked) document.getElementById('supplier_search').focus();
    if (payRadio.checked) document.getElementById('payment_supplier_search').focus();
    if (manyPayRadio.checked) document.getElementById('many_supplier_search').focus();
}

addRadio.addEventListener('change', toggleForms);
payRadio.addEventListener('change', toggleForms);
manyPayRadio.addEventListener('change', toggleForms);

// --- Enter navigation ---
const purchaseFields = ['supplier_search','description','total_amount','grn_search'];
purchaseFields.forEach((id, idx) => {
    document.getElementById(id)?.addEventListener('keydown', e=>{
        if(e.key==='Enter'){
            e.preventDefault();
            const nextField = purchaseFields[idx+1];
            const nextEl = nextField ? document.getElementById(nextField) : null;
            if(nextEl) nextEl.focus();
            else supplierForm.submit();
        }
    });
});

const paymentFields = ['payment_supplier_search', 'payment_description', 'payment_grn_search', 'payment_amount'];
paymentFields.forEach((id, idx)=>{
    document.getElementById(id)?.addEventListener('keydown', e=>{
        if(e.key==='Enter'){
            e.preventDefault();
            const nextField = paymentFields[idx+1];
            const nextEl = nextField ? document.getElementById(nextField) : null;
            if(nextEl) nextEl.focus();
            else paymentForm.submit();
        }
    });
});
// NEW: Enter nav for Many Payments form
document.getElementById('many_supplier_search')?.addEventListener('keydown', e => {
    if(e.key === 'Enter') e.preventDefault(); // Prevent submit, wait for selection
});
document.getElementById('many_payment_description')?.addEventListener('keydown', e => {
    if(e.key === 'Enter') {
        e.preventDefault();
        document.getElementById('many_payment_amount').focus(); // Focus on payment amount
    }
});
document.getElementById('many_payment_amount')?.addEventListener('keydown', e => {
    if(e.key === 'Enter') {
        e.preventDefault();
        const firstCheckbox = document.querySelector('#grnSelectionArea .grn-select-checkbox');
        if (firstCheckbox) {
            firstCheckbox.focus();
        } else {
            document.getElementById('manyPaymentSubmitButton').focus();
        }
    }
});


// --- Live search ---
// *** UPDATED: This function now auto-fills the account_no ***
function setupLiveSearch(inputId, dropdownId, hiddenCodeId, balanceDisplayId, isPurchase=false, isPaymentGrn=false, isManyPayments=false){
    const input=document.getElementById(inputId);
    const dropdown=document.getElementById(dropdownId);
    const hidden=document.getElementById(hiddenCodeId);
    
    if (!input || !dropdown || !hidden) {
        return; 
    }

    const balanceDiv=balanceDisplayId?document.getElementById(balanceDisplayId):null;
    const balanceSpan=balanceDiv?balanceDiv.querySelector('span'):null;
    const hiddenName=isPurchase?document.getElementById('supplier_name'):null;

    const updateBalance=(b)=>{
        if(balanceSpan && balanceDiv){
            balanceSpan.textContent=b;
            balanceSpan.classList.remove('text-success','text-danger');
            const n=parseFloat(b.replace(/,/g,''));
            balanceSpan.classList.add(n>0?'text-danger':'text-success');
            balanceDiv.style.display = 'block';
        }
    };

    input.addEventListener('input', ()=>{
        const f=input.value.toUpperCase().trim();
        let visible=0;
        dropdown.querySelectorAll('button').forEach(btn=>{
            const t=btn.textContent.toUpperCase();
            const match=t.includes(f);
            btn.style.display=match?'block':'none';
            if(match) visible++;
        });
        dropdown.style.display=visible?'block':'none';
        
        if(f===''){ 
            hidden.value=''; 
            if(isPurchase) {
                const supplierIdEl = document.getElementById('supplier_id');
                if(supplierIdEl) supplierIdEl.value=''; 
            }
            if(hiddenName) hiddenName.value=''; 
            if(!isPaymentGrn && balanceDiv) balanceDiv.style.display = 'none';
            
            if(isManyPayments) {
                const grnContainer = document.getElementById('grnSelectionContainer');
                const grnArea = document.getElementById('grnSelectionArea');
                if (grnContainer) grnContainer.style.display = 'none';
                if (grnArea) grnArea.innerHTML = '<p class="text-muted text-center">Select a supplier to see partially paid GRNs.</p>';
                grnSelectionOrder = [];
                recalculateAllocations();
            }
        }
    });

    dropdown.addEventListener('click', e=>{
        if(e.target.matches('button')){
            const btn=e.target;
            const id=btn.getAttribute('data-id');
            const code=btn.getAttribute('data-code');
            const balance=btn.getAttribute('data-balance')||'0.00';
            const name=btn.getAttribute('data-name');
            const account = btn.getAttribute('data-account') || ''; // *** Get account_no ***

            if(inputId==='grn_search'||inputId==='payment_grn_search'){
                input.value=btn.textContent.trim(); 
                hidden.value=id; 
                dropdown.style.display='none'; 
                if(inputId==='grn_search') document.getElementById('submitButton').focus();
                else if(inputId==='payment_grn_search') document.getElementById('payment_amount').focus();
                return; 
            }

            input.value=`${code || ''} ${name?'— '+name:''}`.trim();
            hidden.value=code;
            if(isPurchase){ 
                const supplierIdEl = document.getElementById('supplier_id');
                if(supplierIdEl) supplierIdEl.value=id; 
                if(hiddenName) hiddenName.value=name||''; 
            }

            // *** UPDATED: Auto-fill account number ***
            if (inputId === 'payment_supplier_search') {
                const accountInput = document.getElementById('payment_account_no');
                if (accountInput) accountInput.value = account;
            } else if (inputId === 'many_supplier_search') {
                 const accountInput = document.getElementById('many_account_no');
                if (accountInput) accountInput.value = account;
            }
            // *** END OF UPDATE ***
            
            updateBalance(balance);
            dropdown.style.display='none';
            
            if(isPurchase) document.getElementById('description').focus();
            else if(isManyPayments) {
                document.getElementById('many_payment_description').focus();
                fetchUnpaidGrns(code);
                grnSelectionOrder = [];
                recalculateAllocations();
            }
            else document.getElementById('payment_description').focus();
        }
    });
    document.addEventListener('click', e=>{ 
        if(!dropdown.contains(e.target)&&e.target!==input) {
            dropdown.style.display='none'; 
        }
    });
}

// --- Initialize live searches ---
setupLiveSearch('supplier_search','supplierDropdown','supplier_code','purchaseBalanceDisplay',true);
setupLiveSearch('grn_search','grnDropdown','grn_id');
setupLiveSearch('payment_supplier_search','paymentSupplierDropdown','payment_supplier_code','paymentBalanceDisplay');
setupLiveSearch('payment_grn_search','paymentGrnDropdown','payment_grn_id', null, false, true);
setupLiveSearch('many_supplier_search', 'manySupplierDropdown', 'many_supplier_code', 'manyBalanceDisplay', false, false, true);


// --- NEW: "Many Payments" Logic (Allocation Model) ---
const grnSelectionContainer = document.getElementById('grnSelectionContainer');
const grnSelectionArea = document.getElementById('grnSelectionArea');
const totalSelectedDisplay = document.getElementById('totalSelectedDisplay');
const totalSelectedSpan = totalSelectedDisplay.querySelector('span');
const manyPaymentSubmitButton = document.getElementById('manyPaymentSubmitButton');
const manyPaymentAmountInput = document.getElementById('many_payment_amount');
const paymentRemainingDisplay = document.getElementById('paymentRemainingDisplay');
const paymentRemainingSpan = paymentRemainingDisplay.querySelector('span');
const grnPaymentInputsContainer = document.getElementById('grnPaymentInputsContainer');

let grnSelectionOrder = []; // This array tracks the *order* of selection

function fetchUnpaidGrns(supplierCode) {
    if (!grnSelectionContainer || !grnSelectionArea) return;

    grnSelectionContainer.style.display = 'block';
    grnSelectionArea.innerHTML = '<p class="text-info text-center">Fetching partially paid GRNs...</p>';
    
    const url = `{{ route('suppliers.getUnpaidGrns', ['supplier_code' => '_PLACEHOLDER_']) }}`.replace('_PLACEHOLDER_', encodeURIComponent(supplierCode)); 

    fetch(url, {
        headers: {
            'X-CSRF-TOKEN': CSRF_TOKEN,
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) throw new Error('Network response was not ok');
        return response.json();
    })
    .then(data => {
        if (data.success && data.grns.length > 0) {
            grnSelectionArea.innerHTML = ''; // Clear loading message
            data.grns.forEach(grn => {
                const remaining = parseFloat(grn.remaining_balance);
                const total = parseFloat(grn.total_amount);
                
                const grnRow = `
                <div class="form-check">
                    <input class="form-check-input grn-select-checkbox" type="checkbox" style="margin-left: 10px;"
                           value="${grn.grn_id}" data-remaining="${remaining.toFixed(2)}">
                    <label class="form-check-label d-flex justify-content-between flex-wrap">
                        <span class="me-3">
                            <strong>${grn.grn_code}</strong> (Date: ${grn.date})
                        </span>
                        <span class="text-nowrap">
                            Total: ${total.toFixed(2)} | 
                            <strong class="text-danger">Remaining: ${remaining.toFixed(2)}</strong>
                            <strong class="text-primary allocated-amount ms-2"></strong>
                        </span>
                    </label>
                </div>`;
                grnSelectionArea.insertAdjacentHTML('beforeend', grnRow);
            });
        } else {
            grnSelectionArea.innerHTML = '<p class="text-muted text-center">No partially paid GRNs found for this supplier.</p>';
        }
    })
    .catch(err => {
        console.error('Error fetching GRNs:', err);
        grnSelectionArea.innerHTML = '<p class="text-danger text-center">Error loading GRNs. Please try again.</p>';
    });
}

// Event listener for checkbox changes
grnSelectionArea?.addEventListener('change', e => {
    if (e.target.classList.contains('grn-select-checkbox')) {
        const checkbox = e.target;
        const grnId = checkbox.value;

        if (checkbox.checked) {
            if (!grnSelectionOrder.includes(grnId)) {
                grnSelectionOrder.push(grnId);
            }
        } else {
            grnSelectionOrder = grnSelectionOrder.filter(id => id !== grnId);
        }
        recalculateAllocations();
    }
});

// Event listener for typing in the payment amount field
manyPaymentAmountInput?.addEventListener('input', recalculateAllocations);

function recalculateAllocations() {
    if (!grnSelectionArea || !manyPaymentAmountInput || !grnPaymentInputsContainer) return;

    let paymentPool = parseFloat(manyPaymentAmountInput.value) || 0;
    let totalAllocated = 0;

    grnPaymentInputsContainer.innerHTML = '';
    document.querySelectorAll('#grnSelectionArea .allocated-amount').forEach(span => {
        span.textContent = '';
    });

    for (const grnId of grnSelectionOrder) {
        const checkbox = grnSelectionArea.querySelector(`input.grn-select-checkbox[value="${grnId}"]`);
        
        const label = checkbox.nextElementSibling;
        const displaySpan = label.querySelector('.allocated-amount');
        
        const grnRemaining = parseFloat(checkbox.dataset.remaining);
        let amountToPay = 0;

        if (paymentPool > 0) {
            if (paymentPool >= grnRemaining) {
                amountToPay = grnRemaining;
            } else {
                amountToPay = paymentPool;
            }

            paymentPool -= amountToPay;
            totalAllocated += amountToPay;

            displaySpan.textContent = `(Allocating: ${amountToPay.toFixed(2)})`;

            const hiddenInput = document.createElement('input');
            hiddenInput.type = 'hidden';
            hiddenInput.name = `grn_payments[${grnId}]`; 
            hiddenInput.value = amountToPay.toFixed(2);
            grnPaymentInputsContainer.appendChild(hiddenInput);

        } else {
            displaySpan.textContent = '(No payment left)';
        }
    }
    
    if (totalAllocated > 0) {
        totalSelectedSpan.textContent = totalAllocated.toFixed(2);
        totalSelectedDisplay.style.display = 'block';
    } else {
        totalSelectedDisplay.style.display = 'none';
    }

    const originalPayment = parseFloat(manyPaymentAmountInput.value) || 0;
    const finalRemaining = originalPayment - totalAllocated;

    if (originalPayment > 0) {
        paymentRemainingSpan.textContent = finalRemaining.toFixed(2);
        paymentRemainingDisplay.style.display = 'block';

        paymentRemainingSpan.classList.remove('text-success', 'text-danger', 'text-info');
        if (finalRemaining < 0) {
            paymentRemainingSpan.classList.add('text-danger'); 
        } else if (finalRemaining > 0) {
            paymentRemainingSpan.classList.add('text-success');
        } else {
            paymentRemainingSpan.classList.add('text-info');
        }
    } else {
        paymentRemainingDisplay.style.display = 'none';
    }

    manyPaymentSubmitButton.disabled = (totalAllocated <= 0);
    updateDescriptionField();
}


// --- Transaction history modal & Edit functions ---
document.addEventListener('DOMContentLoaded', ()=>{
    const modalEl=document.getElementById('transactionModal');
    const tableBody=document.getElementById('transactionHistoryBody');
    const grnSummaryBody=document.getElementById('grnPaymentSummaryBody'); 
    const mainTableBody = document.getElementById('supplierTableBody');

    let transactionModal;
    if(typeof bootstrap!=='undefined' && bootstrap.Modal) {
        transactionModal=new bootstrap.Modal(modalEl);
    }

    mainTableBody?.addEventListener('click', e=>{
        if(e.target.classList.contains('view-history-link')){
            const code=e.target.getAttribute('data-supplier-code');
            fetchTransactionHistory(code);
        }
        if(e.target.classList.contains('editButton')){
            const tr=e.target.closest('tr');
            if (!tr) return;
            const dataString = tr.getAttribute('data-transaction');
            try {
                const data=JSON.parse(dataString);
                populateFormForEdit(data);
            } catch (err) {
                console.error("Failed to parse transaction data:", err, dataString);
                alert("Error: Could not load data for editing.");
            }
        }
    });

    function fetchTransactionHistory(code){
        if (!tableBody || !grnSummaryBody) return;
        
        const url = `{{ route('suppliers2.transactions') }}?supplier_code=${encodeURIComponent(code)}`;
        tableBody.innerHTML='<tr><td colspan="7" class="text-center">Loading transactions...</td></tr>';
        grnSummaryBody.innerHTML='<tr><td colspan="5" class="text-center">Loading GRN payments...</td></tr>';

        fetch(url,{ 
            headers:{
                'X-CSRF-TOKEN':CSRF_TOKEN,
                'X-Requested-With':'XMLHttpRequest',
                'Accept':'application/json'
            } 
        })
            .then(r=>r.json())
            .then(data=>{
                document.getElementById('modal_supplier_code').textContent=data.supplier_code;
                document.getElementById('modal_supplier_name').textContent=data.supplier_name;
                document.getElementById('modal_supplier_email').textContent=data.supplier_email || 'N/A';
                document.getElementById('modal_supplier_phone').textContent=data.supplier_phone || 'N/A';
                document.getElementById('modal_supplier_address').textContent=data.supplier_address || 'N/A';
                document.getElementById('modal_total_purchases').textContent=data.total_purchases;
                document.getElementById('modal_total_payments').textContent=data.total_payments;
                
                const bEl=document.getElementById('modal_remaining_balance');
                bEl.textContent=data.remaining_balance;
                bEl.classList.remove('text-success','text-danger');
                bEl.classList.add(parseFloat(data.remaining_balance.replace(/,/g,''))>0?'text-danger':'text-success');
                
                tableBody.innerHTML='';
               if (data.history && data.history.length > 0) {
                    data.history.forEach(item => {
                        
                        const slipLink = item.bank_slip_path 
                            ? `<a href="${item.bank_slip_path}" target="_blank" class="btn btn-sm btn-outline-success">View Slip</a>` 
                            : 'N/A';

                        const row = `
                        <tr>
                            <td>${item.date}</td>
                            <td>${item.type}</td>
                            <td>${item.description}</td>
                            <td>${item.grn_no}</td>
                            <td class="${item.class}">${item.amount}</td>
                            <td class="text-end">${item.balance}</td>
                            <td>${slipLink}</td>
                        </tr>`;
                        tableBody.insertAdjacentHTML('beforeend', row);
                    });
                } else {
                    tableBody.innerHTML = '<tr><td colspan="7" class="text-center text-muted">No transactions found.</td></tr>';
                }

                grnSummaryBody.innerHTML = '';
                if(data.grn_payment_summary && data.grn_payment_summary.length > 0) {
                    data.grn_payment_summary.forEach(item => {
                        const remainingClass = parseFloat(item.remaining.replace(/,/g,'')) > 0 ? 'text-danger' : 'text-success';
                        const summaryRow = `<tr>
                            <td>${item.grn_code}</td>
                            <td class="text-end text-primary">Rs. ${item.grn_total}</td>
                            <td class="text-end text-success">Rs. ${item.total_paid}</td>
                            <td class="text-end ${remainingClass}">Rs. ${item.remaining}</td>
                            <td>${item.last_payment_date}</td>
                        </tr>`;
                        grnSummaryBody.insertAdjacentHTML('beforeend', summaryRow);
                    });
                } else {
                    grnSummaryBody.innerHTML = '<tr><td colspan="5" class="text-center text-muted">No GRN payments recorded.</td></tr>';
                }
                
                if(transactionModal) transactionModal.show();
            }).catch(err=>{
                console.error(err); 
                tableBody.innerHTML='<tr><td colspan="7" class="text-center text-danger">Error loading data.</td></tr>';
                grnSummaryBody.innerHTML='<tr><td colspan="5" class="text-center text-danger">Error loading GRN summary data.</td></tr>'; 
            });
    }

    const cancelEditBtn=document.getElementById('cancelEditButton');
    function populateFormForEdit(data){
        if (!data) return;
        
        addRadio.checked=true; 
        payRadio.checked=false; 
        manyPayRadio.checked = false;
        toggleForms();
        
        document.getElementById('transaction_id').value=data.id;
        document.getElementById('supplier_search').value=`${data.supplier_code || ''} ${data.supplier_name ? '— '+data.supplier_name : ''}`;
        document.getElementById('supplier_id').value=data.existing_supplier_id||'';
        document.getElementById('supplier_code').value=data.supplier_code;
        document.getElementById('supplier_name').value=data.supplier_name;
        document.getElementById('description').value=data.description;
        document.getElementById('total_amount').value=data.total_amount;
        
        const grnSearch = document.getElementById('grn_search');
        const grnId = document.getElementById('grn_id');
        
        let grnDisplayText = '';
        if (data.grn_id && data.grn) {
            grnDisplayText = data.grn.code || '';
        } else if (data.grn_id) {
            const grnOption = document.querySelector(`#grnDropdown button[data-id="${data.grn_id}"]`);
            if (grnOption) {
                grnDisplayText = grnOption.textContent.trim();
            } else {
                grnDisplayText = `GRN ID: ${data.grn_id}`;
            }
        }

        grnSearch.value = grnDisplayText;
        grnId.value=data.grn_id||'';
        
        document.getElementById('submitButton').textContent='Update Purchase';
        if(cancelEditBtn) {
            cancelEditBtn.style.display='inline-block';
            cancelEditBtn.onclick=()=>{ 
                window.location.href = `{{ route('suppliers2.index') }}`; 
            }
        }
        window.scrollTo(0, 0);
    }
    
    const searchInput = document.getElementById('supplierTableSearch');
    let searchTimeout;

    searchInput?.addEventListener('keyup', function() {
        clearTimeout(searchTimeout);
        const keyword = this.value.trim();
        
        searchTimeout = setTimeout(() => {
            fetchTableData(keyword);
        }, 300);
    });

    function fetchTableData(keyword) {
        const url = `{{ route('suppliers2.index') }}` + (keyword ? `?search=${encodeURIComponent(keyword)}` : '');

        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'text/html'
            }
        })
        .then(res => res.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');
            const newBody = doc.querySelector('#supplierTableBody');

            if (newBody && mainTableBody) {
                mainTableBody.innerHTML = newBody.innerHTML;
            }
        })
        .catch(err => console.error('Error fetching table data:', err));
    }

    // Initial form toggle on load
    toggleForms();
});

// --- Payment Method Toggles ---
const paymentChequeDetails = document.getElementById('payment_cheque_details');
const paymentBankDepositDetails = document.getElementById('payment_bank_deposit_details'); // NEW
const paymentMethodRadios = document.querySelectorAll('input[name="payment_method"]');

paymentMethodRadios.forEach(radio => {
    radio.addEventListener('change', function() {
        const isCheque = this.value === 'cheque';
        const isBankDeposit = this.value === 'bank_deposit';
        
        if (paymentChequeDetails) {
            paymentChequeDetails.style.display = isCheque ? 'block' : 'none';
        }
        if (paymentBankDepositDetails) {
            paymentBankDepositDetails.style.display = isBankDeposit ? 'block' : 'none';
        }
        updateDescriptionField();
    });
});

const manyChequeDetails = document.getElementById('many_cheque_details');
const manyBankDepositDetails = document.getElementById('many_bank_deposit_details'); // NEW
const manyPaymentMethodRadios = document.querySelectorAll('input[name="many_payment_method"]');

manyPaymentMethodRadios.forEach(radio => {
    radio.addEventListener('change', function() {
        const isCheque = this.value === 'cheque';
        const isBankDeposit = this.value === 'bank_deposit';

        if (manyChequeDetails) {
            manyChequeDetails.style.display = isCheque ? 'block' : 'none';
        }
        if (manyBankDepositDetails) {
            manyBankDepositDetails.style.display = isBankDeposit ? 'block' : 'none';
        }
        updateDescriptionField(); // Update description on change
    });
});


// --- Auto-fill description based on radio selection ---
function updateDescriptionField() {
    const purchaseDescriptionInput = document.getElementById('description');
    const paymentDescriptionInput = document.getElementById('payment_description');
    const manyPaymentDescriptionInput = document.getElementById('many_payment_description');
    
    if (addRadio.checked) {
        if(purchaseDescriptionInput && purchaseDescriptionInput.value === '') {
            purchaseDescriptionInput.value = 'Purchase from Supplier';
        }
    } else if (payRadio.checked) {
        const paymentMethod = document.querySelector('input[name="payment_method"]:checked').value;
        if(paymentDescriptionInput) {
            if (paymentMethod === 'cheque') {
                paymentDescriptionInput.value = 'Cheque Payment to Supplier';
            } else if (paymentMethod === 'bank_deposit') {
                paymentDescriptionInput.value = 'Bank Deposit to Supplier';
            } else {
                paymentDescriptionInput.value = 'Cash Payment to Supplier';
            }
        }
    } else if (manyPayRadio.checked) {
        const paymentMethod = document.querySelector('input[name="many_payment_method"]:checked').value;
        const anySelected = document.querySelectorAll('#grnPaymentInputsContainer input[type="hidden"]').length > 0;
        
        if(manyPaymentDescriptionInput) {
            if (anySelected) {
                if (paymentMethod === 'cheque') {
                    manyPaymentDescriptionInput.value = 'Cheque Payment for selected GRNs';
                } else if (paymentMethod === 'bank_deposit') {
                    manyPaymentDescriptionInput.value = 'Bank Deposit for selected GRNs';
                } else {
                    manyPaymentDescriptionInput.value = 'Cash Payment for selected GRNs';
                }
            } else {
                 // Don't clear it if the user is typing
            }
        }
    }
}
</script>
@endsection