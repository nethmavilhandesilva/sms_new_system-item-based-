@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99 !important;
        font-family: Arial, sans-serif;
    }

    .container {
        padding: 20px;
    }

    h3 {
        margin-bottom: 20px;
        font-weight: bold;
        color: #004d00;
    }

    /* Form Filters */
    form .form-label {
        font-weight: bold;
    }

    form .form-control, form .form-select {
        min-width: 150px;
    }

    form .d-flex.gap-2 button, form .d-flex.gap-2 a {
        min-width: 80px;
    }

    /* Table Styling */
    table {
        background-color: #ffffff;
    }

    table thead {
        background-color: #004d00;
        color: #fff;
        font-weight: bold;
    }

    table tbody tr:hover {
        background-color: #e6ffe6;
        cursor: pointer;
    }

    /* Code search suggestions */
    #codeSuggestions li:hover {
        background-color: #d4f7d4;
        cursor: pointer;
    }

    /* Modal styling */
    .modal-header {
        font-weight: bold;
    }

    .table-secondary {
        background-color: #f0f0f0 !important;
    }
</style>
<style>
/* =========================================
   COMMON STICKY TABLE CODE (HANDLES ALL REPORTS)
   ========================================= */

/* This gives tables inside .table-responsive a scrollbar */
.table-responsive {
    max-height: 80vh; /* 80% of the screen height */
    overflow-y: auto;
}

/* This selector now targets tables in ALL your report types:
  1. .table-responsive > table (like your Adjustments report)
  2. .custom-card > table (like your first GRN Sales report)
  3. .container > table (like this new report)
*/
.table-responsive > table > thead th,
.custom-card > table > thead th,
.container > table > thead th {
    position: -webkit-sticky; /* For Safari */
    position: sticky;
    top: 0;
    z-index: 10;
}

/* This rule targets the footers in all report styles */
.table-responsive > table > tfoot tr,
.custom-card > table > tfoot tr,
.container > table > tfoot tr {
    position: -webkit-sticky; /* For Safari */
    position: sticky;
    bottom: 0;
    z-index: 10;
}
</style>

<div class="container">
    <h3>GRN Sales Report</h3>

    {{-- Filters Inline --}}
    <form method="GET" action="{{ route('grn.sales.report') }}" class="mb-4 d-flex flex-wrap align-items-end gap-3">

        <div>
            <label for="start_date" class="form-label mb-1">Start Date</label>
            <input type="date" name="start_date" id="start_date" class="form-control" value="{{ request('start_date') }}">
        </div>

        <div>
            <label for="end_date" class="form-label mb-1">End Date</label>
            <input type="date" name="end_date" id="end_date" class="form-control" value="{{ request('end_date') }}">
        </div>

        <div>
            <label for="supplier_code" class="form-label mb-1 fw-bold">Supplier Code</label>
            <select name="supplier_code" id="supplier_code" class="form-select">
                <option value="">All</option>
                <option value="L" {{ request('supplier_code') == 'L' ? 'selected' : '' }}>L</option>
                <option value="A" {{ request('supplier_code') == 'A' ? 'selected' : '' }}>A</option>
            </select>
        </div>

        <div class="position-relative" style="width: 250px;">
            <label for="codeSearch" class="form-label mb-1 fw-bold">Search by Code</label>
            <input type="text" id="codeSearch" class="form-control text-uppercase" placeholder="Type to filter by code...">
            <ul id="codeSuggestions" class="list-group position-absolute w-100 shadow-sm" style="display: none; z-index:10; max-height:200px; overflow-y:auto;"></ul>
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-success">Filter</button>
            <a href="{{ route('grn.sales.report') }}" class="btn btn-secondary">Reset</a>
        </div>
    </form>

    {{-- GRN Table --}}
    <table class="table table-sm table-bordered" id="grnTable">
        <thead style="background-color: #cce5ff; color: #004085; font-weight: bold;">
            <tr>
                <th>Code / Item</th>
                <th>Sold Weight</th>
                <th>Sold Packs</th>
                <th>Selling Price</th>
                <th>Total Cost</th>
                <th>Net Sale</th>
                <th>Profit / Loss</th>
            </tr>
        </thead>
        <tbody>
            @foreach($report as $row)
                {{-- *** THIS IS THE MODIFIED LINE *** --}}
                @php $profitLoss = ($row->total_cost == 0) ? 0 : ($row->netsale - $row->total_cost); @endphp
                
                <tr class="clickable-row" data-code="{{ $row->code }}">
                    <td class="code">{{ $row->code }} - {{ $row->item_name }}</td>
                    <td>{{ number_format($row->sold_weight, 3) }}</td>
                    <td>{{ number_format($row->sold_packs, 0) }}</td>
                    <td>{{ number_format($row->selling_price, 2) }}</td>
                    <td>{{ number_format($row->total_cost, 2) }}</td>
                    <td>{{ number_format($row->netsale, 2) }}</td>
                    <td style="color: {{ $profitLoss < 0 ? 'red' : 'green' }}">
                        {{ number_format(abs($profitLoss), 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>

        {{-- Totals row --}}
        <tfoot class="fw-bold table-secondary">
            <tr>
                <td class="text-end">Totals:</td>
                <td></td>
                <td></td>
                <td id="totalSellingPrice">0.00</td>
                <td id="totalCost">0.00</td>
                <td id="totalNetSale">0.00</td>
                <td id="totalProfitLoss">0.00</td>
            </tr>
        </tfoot>
    </table>

    {{-- 💰 Loan Summary Section --}}
    <div class="card my-4 shadow-sm">
        <div class="card-header fw-bold bg-light">
            Loan Summary
        </div>
        <div class="card-body">
            <div class="row text-center">
                <div class="col-md-6 border-end">
                    <h5 class="card-title text-muted">Today's Loans</h5>
                    <p class="card-text fs-3 fw-bold text-primary" 
                       id="todayLoanTotal" 
                       data-bs-toggle="modal" 
                       data-bs-target="#loanModal" 
                       data-type="today" 
                       style="cursor: pointer; text-decoration: underline;">
                         {{ number_format(abs($todayLoanTotal), 2) }}
                    </p>
                </div>
                <div class="col-md-6">
                    <h5 class="card-title text-muted">Old Loans</h5>
                    <p class="card-text fs-3 fw-bold text-danger" 
                       id="oldLoanTotal" 
                       data-bs-toggle="modal" 
                       data-bs-target="#loanModal" 
                       data-type="old" 
                       style="cursor: pointer; text-decoration: underline;">
                         {{ number_format(abs($oldLoanTotal), 2) }}
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- 💸 NEW: Expense Summary Section --}}
    <div class="card my-4 shadow-sm">
        <div class="card-header fw-bold bg-light">
            Expenses Summary
        </div>
        <div class="card-body">
            <ul class="list-group list-group-flush">
                @forelse($expenseCategories as $expense)
                    <li class="list-group-item d-flex justify-content-between align-items-center"
                        data-bs-toggle="modal"
                        data-bs-target="#expenseModal"
                        data-category="{{ $expense->category }}"
                        style="cursor: pointer;">
                        
                        <span class="text-capitalize">{{ $expense->category }}</span>
                        
                        <span class="badge bg-danger rounded-pill fs-6">
                            {{ number_format(abs($expense->total_amount), 2) }}
                        </span>
                    </li>
                @empty
                    <li class="list-group-item text-muted">No expenses found for this period.</li>
                @endforelse
            </ul>
        </div>
    </div>

</div> {{-- End of .container --}}

{{-- Sales Modal --}}
<div class="modal fade" id="salesModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-success text-white">
        <h5 class="modal-title" id="modalTitle">Sales Records</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
      </div>
      <div class="modal-body">
        {{-- GRN Entry Info --}}
        <div class="mb-3">
            <strong id="modalGrnInfo"></strong>
        </div>
        <table class="table table-sm table-bordered" id="salesTable">
            <thead>
                <tr>
                    <th>Code</th>
                    <th>Item Name</th>
                    <th>Weight</th>
                    <th>Price / KG</th>
                    <th>Total</th>
                    <th>Packs</th>
                    <th>Bill No</th>
                </tr>
            </thead>
            <tbody></tbody>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- 💰 Loan Details Modal --}}
<div class="modal fade" id="loanModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-dark text-white">
        <h5 class="modal-title" id="loanModalTitle">Loan Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-sm table-bordered" id="loanDetailsTable">
          <thead>
            <tr>
              <th>Customer Name</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            {{-- JS will populate this --}}
            <tr><td colspan="2">Loading...</td></tr>
          </tbody>
          <tfoot class="fw-bold table-secondary">
              <tr>
                  <td class="text-end">Total:</td>
                  <td id="loanModalTotal">0.00</td>
              </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>

{{-- 💸 NEW: Expense Details Modal --}}
<div class="modal fade" id="expenseModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-lg modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header bg-danger text-white">
        <h5 class="modal-title" id="expenseModalTitle">Expense Details</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <table class="table table-sm table-bordered" id="expenseDetailsTable">
          <thead>
            <tr>
              <th>Description</th>
              <th>Amount</th>
            </tr>
          </thead>
          <tbody>
            {{-- JS will populate this --}}
            <tr><td colspan="2" class="text-center">Loading...</td></tr>
          </tbody>
          <tfoot class="fw-bold table-secondary">
              <tr>
                  <td class="text-end">Total:</td>
                  <td id="expenseModalTotal">0.00</td>
              </tr>
          </tfoot>
        </table>
      </div>
    </div>
  </div>
</div>


{{-- JS --}}
<script>
document.addEventListener("DOMContentLoaded", () => {

    const searchInput = document.getElementById("codeSearch");
    const suggestionsBox = document.getElementById("codeSuggestions");
    const tableRows = document.querySelectorAll("#grnTable tbody tr");
    const codes = Array.from(document.querySelectorAll(".code")).map(td => td.textContent.trim().toUpperCase()).filter((v,i,a)=>a.indexOf(v)===i);

    // --- Calculate totals function ---
    function calculateTotals() {
        let totalSelling = 0, totalCost = 0, totalNet = 0, totalProfit = 0;

        document.querySelectorAll("#grnTable tbody tr").forEach(row => {
            if (row.style.display !== "none") {
                const sellingPrice = parseFloat(row.children[3].textContent.replace(/,/g, '')) || 0;
                const cost = parseFloat(row.children[4].textContent.replace(/,/g, '')) || 0;
                const netSale = parseFloat(row.children[5].textContent.replace(/,/g, '')) || 0;
                // Corrected profit calculation: it should use the sign from the cell
                const profitText = row.children[6].textContent.replace(/,/g, '');
                const isNegative = row.children[6].style.color === 'red';
                const profitLoss = parseFloat(profitText) * (isNegative ? -1 : 1) || 0;

                totalSelling += sellingPrice;
                totalCost += cost;
                totalNet += netSale;
                totalProfit += profitLoss; // Use the signed value
            }
        });

        document.getElementById("totalSellingPrice").textContent = totalSelling.toFixed(2);
        document.getElementById("totalCost").textContent = totalCost.toFixed(2);
        document.getElementById("totalNetSale").textContent = totalNet.toFixed(2);
        
        // Format total profit/loss with color
        const totalProfitEl = document.getElementById("totalProfitLoss");
        totalProfitEl.textContent = Math.abs(totalProfit).toFixed(2);
        totalProfitEl.style.color = totalProfit < 0 ? 'red' : 'green';
    }

    // Initial totals on page load
    calculateTotals();

    // --- Code Search ---
    searchInput.addEventListener("input", () => {
        const searchValue = searchInput.value.trim().toUpperCase();
        const matches = codes.filter(code => code.startsWith(searchValue));
        suggestionsBox.innerHTML = "";
        if(searchValue && matches.length>0){
            matches.forEach(code=>{
                const li=document.createElement("li");
                li.classList.add("list-group-item","list-group-item-action");
                li.textContent = code;
                li.onclick = ()=>{
                    searchInput.value = code;
                    suggestionsBox.style.display = "none";
                    filterTable(code);
                    calculateTotals(); // Recalculate after filtering
                };
                suggestionsBox.appendChild(li);
            });
            suggestionsBox.style.display = "block";
        } else {
            suggestionsBox.style.display = "none";
        }
        filterTable(searchValue);
        calculateTotals(); // Recalculate after filtering
    });

    document.addEventListener("click", e=>{
        if(!suggestionsBox.contains(e.target) && e.target!==searchInput)
            suggestionsBox.style.display = "none";
    });

    function filterTable(searchValue){
        tableRows.forEach(row=>{
            const code = row.querySelector(".code").textContent.trim().toUpperCase();
            row.style.display = code.startsWith(searchValue)?"":"none";
        });
    }

    // --- Row click to modal ---
    tableRows.forEach(row=>{
        row.addEventListener("click", ()=>{
            const code = row.dataset.code;
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            fetch(`{{ route('grn.sales.fetch') }}?code=${code}&start_date=${startDate}&end_date=${endDate}`)
                .then(res => res.json())
                .then(data => {
                    if(data.grn){
                        const grn = data.grn;
                        document.getElementById('modalGrnInfo').textContent = `${grn.code} - ${grn.item_name} | Total Packs: ${grn.packs} | Total Weight: ${parseFloat(grn.weight).toFixed(3)}`;
                    }

                    const tbody = document.querySelector("#salesTable tbody");
                    tbody.innerHTML = "";

                    let totalWeight = 0;
                    let totalAmount = 0;
                    let totalPacks = 0;

                    data.sales.forEach(item => {
                        const weight = parseFloat(item.weight) || 0;
                        const total = parseFloat(item.total) || 0;
                        const packs = parseInt(item.packs) || 0;

                        totalWeight += weight;
                        totalAmount += total;
                        totalPacks += packs;

                        tbody.innerHTML += `
                            <tr style="color:${total<0?'red':'inherit'};">
                                <td>${item.code}</td>
                                <td>${item.item_name}</td>
                                <td>${weight.toFixed(3)}</td>
                                <td>${parseFloat(item.price_per_kg).toFixed(2)}</td>
                                <td>${total.toFixed(2)}</td>
                                <td>${packs}</td>
                                <td>${item.bill_no}</td>
                            </tr>
                        `;
                    });

                    tbody.innerHTML += `
                        <tr class="fw-bold table-secondary">
                            <td colspan="2" class="text-end">Totals:</td>
                            <td>${totalWeight.toFixed(3)}</td>
                            <td></td>
                            <td>${totalAmount.toFixed(2)}</td>
                            <td>${totalPacks}</td>
                            <td></td>
                        </tr>
                    `;

                    new bootstrap.Modal(document.getElementById('salesModal')).show();
                });
        });
    });

    // --- 💰 NEW: Loan Modal Logic ---
    const loanModal = document.getElementById('loanModal');
    if (loanModal) {
        loanModal.addEventListener('show.bs.modal', event => {
            const triggerElement = event.relatedTarget; // The <p> tag that was clicked
            const loanType = triggerElement.dataset.type; // 'today' or 'old'
            
            const modalTitle = loanModal.querySelector('#loanModalTitle');
            const modalTbody = loanModal.querySelector('#loanDetailsTable tbody');
            const modalTotal = loanModal.querySelector('#loanModalTotal');

            // Set title and loading state
            modalTitle.textContent = `${loanType.charAt(0).toUpperCase() + loanType.slice(1)} Loan Details`;
            modalTbody.innerHTML = '<tr><td colspan="2" class="text-center">Loading...</td></tr>';
            modalTotal.textContent = '0.00';

            // Get dates from the form
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            // Build URL for fetching data
            const url = new URL('{{ route('grn.sales.fetchLoans') }}');
            url.searchParams.append('loan_type', loanType);
            if (startDate) url.searchParams.append('start_date', startDate);
            if (endDate) url.searchParams.append('end_date', endDate);

            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    modalTbody.innerHTML = ""; // Clear loading
                    let totalAmount = 0;

                    if (data.length === 0) {
                         modalTbody.innerHTML = '<tr><td colspan="2" class="text-center">No records found.</td></tr>';
                    } else {
                        data.forEach(item => {
                            const amount = parseFloat(item.amount) || 0;
                            totalAmount += amount;
                            modalTbody.innerHTML += `
                                <tr>
                                    <td>${item.short_name || 'N/A'}</td>
                                    <td>${Math.abs(amount).toFixed(2)}</td>
                                </tr>
                            `;
                        });
                    }
                    // Update the modal's total
                    modalTotal.textContent = Math.abs(totalAmount).toFixed(2);
                })
                .catch(error => {
                    console.error('Error fetching loan details:', error);
                    modalTbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Error loading data.</td></tr>';
                });
        });
    }
    // --- End of new Loan Modal Logic ---


    // --- 💸 NEW: Expense Modal Logic ---
    const expenseModal = document.getElementById('expenseModal');
    if (expenseModal) {
        expenseModal.addEventListener('show.bs.modal', event => {
            const triggerElement = event.relatedTarget; // The <li> that was clicked
            const category = triggerElement.dataset.category; // 'salary', 'rent', etc.
            
            const modalTitle = expenseModal.querySelector('#expenseModalTitle');
            const modalTbody = expenseModal.querySelector('#expenseDetailsTable tbody');
            const modalTotal = expenseModal.querySelector('#expenseModalTotal');

            // Set title and loading state
            modalTitle.textContent = `Details for: ${category}`;
            modalTbody.innerHTML = '<tr><td colspan="2" class="text-center">Loading...</td></tr>';
            modalTotal.textContent = '0.00';

            // Get dates from the form
            const startDate = document.getElementById('start_date').value;
            const endDate = document.getElementById('end_date').value;

            // Build URL for fetching data
            const url = new URL('{{ route('grn.sales.fetchExpenses') }}');
            url.searchParams.append('category', category);
            if (startDate) url.searchParams.append('start_date', startDate);
            if (endDate) url.searchParams.append('end_date', endDate);

            fetch(url)
                .then(res => {
                    if (!res.ok) throw new Error('Network response was not ok');
                    return res.json();
                })
                .then(data => {
                    modalTbody.innerHTML = ""; // Clear loading
                    let totalAmount = 0;

                    if (data.length === 0) {
                         modalTbody.innerHTML = '<tr><td colspan="2" class="text-center">No records found.</td></tr>';
                    } else {
                        data.forEach(item => {
                            const amount = parseFloat(item.amount) || 0;
                            totalAmount += amount;
                            modalTbody.innerHTML += `
                                <tr>
                                    <td>${item.description || 'N/A'}</td>
                                    <td>${Math.abs(amount).toFixed(2)}</td>
                                </tr>
                            `;
                        });
                    }
                    // Update the modal's total
                    modalTotal.textContent = Math.abs(totalAmount).toFixed(2);
                })
                .catch(error => {
                    console.error('Error fetching expense details:', error);
                    modalTbody.innerHTML = '<tr><td colspan="2" class="text-center text-danger">Error loading data.</td></tr>';
                });
        });
    }
    // --- End of new Expense Modal Logic ---


});
</script>
@endsection