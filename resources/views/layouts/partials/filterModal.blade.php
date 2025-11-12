<!-- Filter Modal -->
<div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content" style="background-color:#99ff99;">
      <form method="GET" action="{{ route('sales.report') }}">
        <div class="modal-header">
          <h5 class="modal-title" id="filterModalLabel">Filter Sales</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="row g-2">
            {{-- Supplier --}}
            <div class="col-md-6">
              <label for="supplier_code" class="form-label">Supplier</label>
              <select name="supplier_code" id="supplier_code" class="form-select form-select-sm">
                <option value="">-- Select Supplier --</option>
                @foreach($suppliers as $supplier)
                  <option value="{{ $supplier->code }}" {{ request('supplier_code') == $supplier->code ? 'selected' : '' }}>
                    {{ $supplier->code }} - {{ $supplier->name }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Item --}}
            <div class="col-md-6">
              <label for="item_code" class="form-label">Item</label>
              <select name="item_code" id="item_code" class="form-select form-select-sm">
                <option value="">-- Select Item --</option>
                @foreach($items as $item)
                  <option value="{{ $item->no }}" {{ request('item_code') == $item->no ? 'selected' : '' }}>
                    {{ $item->no }} - {{ $item->type }}
                  </option>
                @endforeach
              </select>
            </div>

            {{-- Customer --}}
            <div class="col-md-6">
              <label for="filter_customer_code" class="form-label">පාරිභෝගික කේතය</label>
              <select name="customer_code" id="filter_customer_code" class="form-select form-select-sm select2-customer">
                <option value="">-- සියලුම පාරිභෝගිකයන් --</option>
                @php
                    $customers = \App\Models\Sale::select('customer_code')->distinct()->get();
                @endphp
                @foreach($customers as $customer)
                    <option value="{{ $customer->customer_code }}" {{ request('customer_code') == $customer->customer_code ? 'selected' : '' }}>
                        {{ $customer->customer_code }}
                    </option>
                @endforeach
              </select>
            </div>

            {{-- Bill No --}}
            <div class="col-md-6">
              <label for="filter_bill_no" class="form-label">Bill No</label>
              <select name="bill_no" id="filter_bill_no" class="form-select form-select-sm select2-bill">
                <option value="">-- All Bills --</option>
                @php
                    $billNos = \App\Models\Sale::select('bill_no')->whereNotNull('bill_no')->where('bill_no', '<>', '')->distinct()->get();
                @endphp
                @foreach($billNos as $bill)
                    <option value="{{ $bill->bill_no }}" {{ request('bill_no') == $bill->bill_no ? 'selected' : '' }}>
                        {{ $bill->bill_no }}
                    </option>
                @endforeach
              </select>
            </div>

            {{-- Order By --}}
            <div class="col-md-6">
              <label for="order_by" class="form-label">Order By</label>
              <select name="order_by" id="order_by" class="form-select form-select-sm">
                <option value="">-- Select Order --</option>
                <option value="bill_no" {{ request('order_by') == 'bill_no' ? 'selected' : '' }}>Bill No</option>
                <option value="customer_code" {{ request('order_by') == 'customer_code' ? 'selected' : '' }}>Customer Code</option>
              </select>
            </div>

            {{-- Start Date --}}
            <div class="col-md-6">
              <label for="start_date" class="form-label">Start Date</label>
              <input type="date" name="start_date" id="start_date" class="form-control form-control-sm" value="{{ request('start_date') }}">
            </div>

            {{-- End Date --}}
            <div class="col-md-6">
              <label for="end_date" class="form-label">End Date</label>
              <input type="date" name="end_date" id="end_date" class="form-control form-control-sm" value="{{ request('end_date') }}">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <a href="{{ route('salesemail.report') }}" class="btn btn-info btn-sm">📧 Daily Email Report</a>
          <button type="submit" class="btn btn-success btn-sm">Filter</button>
          <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Close</button>
        </div>
      </form>
    </div>
  </div>
</div>
