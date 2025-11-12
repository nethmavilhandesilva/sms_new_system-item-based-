@extends('layouts.app')


<style>
    body {
        background-color: #99ff99 !important;
    }

    .table-row:hover {
        background-color: #f1f1f1;
        /* A neutral hover is cleaner */
        cursor: pointer;
    }

    /* *** UNREAD GRN UPDATE *** */
    .table-unread {
        /* Highlighting unread rows */
        background-color: #fff3cd !important; /* A light yellow/gold for visibility */
        border-left: 5px solid #ffc107; /* A strong visual cue */
        font-weight: bold;
    }
    .unread-count-badge {
        font-size: 1.2rem;
        padding: 0.25em 0.6em;
    }
</style>
<style>
.table-responsive {
    max-height: 80vh; /* 80% of the screen height */
    overflow-y: auto;
}

.table-responsive > table > thead th,
.custom-card > table > thead th {
    position: -webkit-sticky; /* For Safari */
    position: sticky;
    top: 0;
    z-index: 10;
   /* Inherits the thead's background color */
}

/* This rule targets the footers in BOTH report styles */
.table-responsive > table > tfoot tr,
.custom-card > table > tfoot tr {
    position: -webkit-sticky; /* For Safari */
    position: sticky;
    bottom: 0;
    z-index: 10;
}
</style>


@section('content')
    <div class="container mt-4">

        <h2 class="text-center mb-4 fw-bold text-dark opacity-75">GRN Entry Report</h2>

        {{-- *** UNREAD GRN UPDATE *** Display Unread Count at the top *** --}}
        <div class="d-flex justify-content-center mb-4">
            <span class="badge bg-warning text-dark shadow-sm unread-count-badge">
                Unread GRNs: <span id="unreadCount">{{ $unreadCount ?? count($entries->where('is_read', 0)) }}</span>
            </span>
        </div>

        {{-- Filter Card --}}
        <div class="card shadow-sm mb-4">
            <div class="card-body">
                <form method="GET" action="{{ route('grn.reportfinal') }}" class="row g-3 mb-2">
                    <div class="col-md-2">
                        <label class="form-label">Start Date</label>
                        <input type="date" name="start_date" class="form-control" value="{{ request('start_date') }}">
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">End Date</label>
                        <input type="date" name="end_date" class="form-control" value="{{ request('end_date') }}">
                    </div>
                    <div class="col-md-3 position-relative">
                        <label class="form-label">Search by Code</label>
                        <input type="text" name="code" id="codeSearch" class="form-control" autocomplete="off" placeholder="Type code..." style="text-transform: uppercase;">
                        <ul id="codeList" class="list-group position-absolute w-100"
                            style="z-index:1000; display:none; max-height:200px; overflow-y:auto;"></ul>
                    </div>
                    <div class="col-md-2">
                        <label class="form-label">තොග/සිල්ලර</label>
                        <select name="supplier_filter" class="form-select">
                            <option value="">All</option>
                            <option value="L" {{ request('supplier_filter') == 'L' ? 'selected' : '' }}>L</option>
                            <option value="A" {{ request('supplier_filter') == 'A' ? 'selected' : '' }}>A</option>
                        </select>
                    </div>
                    <div class="col-md-3 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-success w-50">Filter</button>
                        <a href="{{ route('grn.reportfinal') }}" class="btn btn-secondary w-50">Clear</a>
                    </div>
                </form>
            </div>
        </div>

        {{-- Data Table Card --}}
        <div class="card shadow-lg border-0">
            <div class="card-body table-responsive p-0"> 
                <table class="table table-bordered table-striped text-center align-middle" id="grnTable">

                    <thead class="bg-success text-white">
                        <tr>
                            <th>Code</th>
                            <th>SupCode</th>
                            <th>Item Name</th>
                            <th>Packs</th>
                            <th>Weight</th>
                            <th>OP</th>
                            <th>OW</th>
                            <th>Total GRN</th>
                            <th>BP</th>
                            <th>Txn Date</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($entries as $entry)
                            {{-- *** UNREAD GRN UPDATE *** Added conditional class and data-is_read *** --}}
                            <tr class="table-row {{ $entry->is_read == 0 ? 'table-unread' : '' }}"
                                data-id="{{ $entry->id }}"
                                data-code="{{ $entry->code }}"
                                data-item_name="{{ $entry->item_name }}"
                                data-packs="{{ $entry->packs }}"
                                data-weight="{{ $entry->weight }}"
                                data-total_grn="{{ $entry->total_grn }}"
                                data-bp="{{ $entry->BP }}"
                                data-real_supplier_code="{{ $entry->Real_Supplier_code }}"
                                data-is_read="{{ $entry->is_read }}"> {{-- *** UNREAD GRN UPDATE *** --}}

                                <td>{{ $entry->code }}</td>
                                <td>{{ $entry->Real_Supplier_code }}</td>
                                <td>{{ $entry->item_name }}</td>
                                <td>{{ $entry->packs }}</td>
                                <td>{{ $entry->weight }}</td>
                                <td>{{ $entry->original_packs }}</td>
                                <td>{{ $entry->original_weight }}</td>
                                <td>{{ $entry->total_grn }}</td>
                                <td>{{ $entry->BP }}</td>
                                <td>{{ $entry->txn_date }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="12" class="text-muted">No records found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div class="modal fade" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content shadow-lg border-0">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="editModalLabel">Edit GRN Entry</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>

                <div class="modal-body bg-light">

                    {{-- 
                      *** 2. UPDATE HERE ***
                      Added the display block for selected item details
                    --}}
                    <div class="mb-3 p-3 bg-white rounded border shadow-sm">
                        <h5 class="fw-bold text-success mb-2" id="modalItemName"></h5>
                        <div class="d-flex justify-content-between">
                            <span class="text-muted"><strong>Code:</strong> <span id="modalItemCode"></span></span>
                            <span class="text-muted"><strong>SupCode:</strong> <span id="modalSupplierCode"></span></span>
                        </div>
                    </div>

                    <form id="updateForm">
                        @csrf
                        <input type="hidden" id="recordId">
                        <div class="mb-3">
                            <label class="form-label fw-bold">Packs</label>
                            <input type="number" id="packs" class="form-control" step="any">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Weight</label>
                            <input type="number" id="weight" class="form-control" step="any">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">Total GRN</label>
                            <input type="number" id="total_grn" class="form-control" step="any">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-bold">BP</label>
                            <input type="text" id="bp" class="form-control">
                        </div>

                        <div class="mb-3 position-relative">
                            <label class="form-label fw-bold">Real Supplier Code</label>
                            <input type="text" id="real_supplier_search" class="form-control" placeholder="TYPE CODE OR NAME TO SEARCH..." autocomplete="off" oninput="this.value = this.value.toUpperCase();">
                            <input type="hidden" id="real_supplier_code">
                            <ul id="supplierList" class="list-group position-absolute w-100"
                                style="z-index: 1051; display:none; max-height:200px; overflow-y:auto;"></ul>
                        </div>
                    </form>
                </div>

                <div class="modal-footer border-top-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" id="saveChanges" class="btn btn-success">Save Changes</button>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            const codeSearch = document.getElementById("codeSearch");
            const codeList = document.getElementById("codeList");
            const tableRows = document.querySelectorAll(".table-row");

            const supplierSearch = document.getElementById("real_supplier_search");
            const supplierList = document.getElementById("supplierList");
            const realSupplierCodeInput = document.getElementById("real_supplier_code");

            // *** UNREAD GRN UPDATE *** Get initial count element ***
            const unreadCountElement = document.getElementById("unreadCount");
            let unreadCount = parseInt(unreadCountElement.textContent);


            // --- Code Search ---
            codeSearch.addEventListener("keyup", function () {
                const term = this.value.trim();
                if (term.length === 0) {
                    codeList.style.display = "none";
                    return;
                }
                fetch(`/search-codes?term=${term}`)
                    .then(res => res.json())
                    .then(data => {
                        codeList.innerHTML = "";
                        if (data.length > 0) {
                            data.forEach(code => {
                                const li = document.createElement("li");
                                li.textContent = code;
                                li.classList.add("list-group-item", "list-group-item-action");
                                li.addEventListener("click", function () {
                                    codeSearch.value = code;
                                    codeList.style.display = "none";
                                });
                                codeList.appendChild(li);
                            });
                            codeList.style.display = "block";
                        } else {
                            codeList.style.display = "none";
                        }
                    });
            });

            // --- Supplier Search ---
            supplierSearch.addEventListener("keyup", function () {
                const term = this.value.trim();
                if (term.length === 0) {
                    supplierList.style.display = "none";
                    return;
                }

                fetch(`/sms/search-suppliers?term=${term}`)
                    .then(res => res.json())
                    .then(data => {
                        supplierList.innerHTML = "";
                        if (data.length > 0) {
                            data.forEach(supplier => {
                                const li = document.createElement("li");
                                li.textContent = `${supplier.code} - ${supplier.name}`;
                                li.classList.add("list-group-item", "list-group-item-action");
                                li.addEventListener("click", function () {
                                    supplierSearch.value = `${supplier.code} - ${supplier.name}`;
                                    realSupplierCodeInput.value = supplier.code;
                                    supplierList.style.display = "none";
                                });
                                supplierList.appendChild(li);
                            });
                            supplierList.style.display = "block";
                        } else {
                            supplierList.style.display = "none";
                        }
                    });
            });

            // --- Hide dropdowns on outside click ---
            document.addEventListener("click", function (e) {
                if (!codeList.contains(e.target) && e.target !== codeSearch) {
                    codeList.style.display = "none";
                }
                if (!supplierList.contains(e.target) && e.target !== supplierSearch) {
                    supplierList.style.display = "none";
                }
            });

            // --- Table Row Click (to open modal) ---
            tableRows.forEach(row => {
                row.addEventListener("click", function () {
                    
                    {{-- 
                      *** 3. UPDATE HERE ***
                      Added lines to populate the new display elements
                    --}}
                    document.getElementById("modalItemName").textContent = this.dataset.item_name;
                    document.getElementById("modalItemCode").textContent = this.dataset.code;
                    document.getElementById("modalSupplierCode").textContent = this.dataset.real_supplier_code;

                    // --- Populate form inputs ---
                    document.getElementById("recordId").value = this.dataset.id;
                    document.getElementById("packs").value = this.dataset.packs;
                    document.getElementById("weight").value = this.dataset.weight;
                    document.getElementById("total_grn").value = this.dataset.total_grn;
                    document.getElementById("bp").value = this.dataset.bp;

                    const currentSupplierCode = this.dataset.real_supplier_code;
                    document.getElementById("real_supplier_code").value = currentSupplierCode;
                    document.getElementById("real_supplier_search").value = currentSupplierCode;

                    
                    // *** UNREAD GRN UPDATE *** Logic to mark as read and update UI ***
                    const recordId = this.dataset.id;
                    const isRead = this.dataset.is_read;

                    if (isRead === '0') {
                        // Mark the record as read in the database
                        fetch(`/sms/grn-report/mark-read/${recordId}`, { // *** YOU MUST IMPLEMENT THIS ROUTE/CONTROLLER METHOD ***
                            method: "POST",
                            headers: { "Content-Type": "application/json" },
                            body: JSON.stringify({ _token: "{{ csrf_token() }}" }),
                        })
                        .then(res => res.json())
                        .then(resp => {
                            if (resp.success) {
                                // Update UI locally
                                row.classList.remove("table-unread");
                                row.dataset.is_read = '1';
                                unreadCount--;
                                unreadCountElement.textContent = unreadCount;
                            }
                            // Show the modal regardless of the API call's success, 
                            // as the primary action is editing.
                            new bootstrap.Modal(document.getElementById('editModal')).show();
                        })
                        .catch(error => {
                            console.error('Error marking as read:', error);
                            // Show the modal even if the update failed
                            new bootstrap.Modal(document.getElementById('editModal')).show();
                        });
                    } else {
                         // If already read, just show the modal
                        new bootstrap.Modal(document.getElementById('editModal')).show();
                    }
                    
                });
            });

            // --- Save Changes Button ---
            document.getElementById("saveChanges").addEventListener("click", function () {
                const id = document.getElementById("recordId").value;
                const data = {
                    _token: "{{ csrf_token() }}",
                    packs: document.getElementById("packs").value,
                    weight: document.getElementById("weight").value,
                    total_grn: document.getElementById("total_grn").value,
                    BP: document.getElementById("bp").value,
                    Real_Supplier_code: document.getElementById("real_supplier_code").value,
                };

                fetch(`/sms/grn-report/update/${id}`, {
                    method: "POST",
                    headers: { "Content-Type": "application/json" },
                    body: JSON.stringify(data),
                })
                    .then(res => res.json())
                    .then(resp => {
                        if (resp.success) {
                            location.reload();
                        } else {
                            alert("Error updating record.");
                        }
                    });
            });
        });
    </script>
@endsection