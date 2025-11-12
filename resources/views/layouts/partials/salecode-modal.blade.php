<div class="modal fade" id="grnSaleReportModal" tabindex="-1" aria-labelledby="grnSaleReportModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form action="{{ route('report.grn_sale.fetch') }}" method="POST" target="_blank">
            @csrf
            <div class="modal-content" style="background-color: #99ff99;">
                <div class="modal-header">
                    <h5 class="modal-title" id="grnSaleReportModalLabel">📄 GRN කේතය අනුව විකුණුම් වාර්තාව</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    @if ($errors->any())
                        <div class="alert alert-danger">
                            <ul>
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    <div class="mb-3">
                        <label for="grnSearchInput" class="form-label" style="font-weight: bold; color: black;">GRN තොරතුරු තෝරන්න</label>
                        <input type="text" id="grnSearchInput" class="form-control text-uppercase" placeholder="GRN කේතය හෝ අයිතම නාමය ටයිප් කරන්න..." autocomplete="off">
                        
                        <input type="hidden" name="grn_code" id="grn_code_to_submit" required>

                        <div class="search-list border rounded mt-1" id="grnCodeList" style="max-height: 200px; overflow-y: auto; display: none;">
                            @foreach ($entries as $entry)
                                <div class="search-item p-2" style="cursor:pointer;"
                                     data-code="{{ $entry->code }}"
                                     data-supplier-code="{{ $entry->supplier_code }}"
                                     data-item-code="{{ $entry->item_code }}"
                                     data-item-name="{{ $entry->item_name }}"
                                     data-weight="{{ $entry->weight }}"
                                     data-price="{{ $entry->price_per_kg }}"
                                     data-total="{{ $entry->total }}"
                                     data-packs="{{ $entry->packs }}"
                                     data-grn-no="{{ $entry->grn_no }}"
                                     data-txn-date="{{ $entry->txn_date }}"
                                     data-original-weight="{{ $entry->original_weight }}"
                                     data-original-packs="{{ $entry->original_packs }}"
                                     onclick="selectGrnCode(this)">
                                    <strong>{{ $entry->code }}</strong> | {{ $entry->supplier_code }} | {{ $entry->item_code }} |
                                    {{ $entry->item_name }} | {{ $entry->packs }} | {{ $entry->grn_no }} |
                                    {{ $entry->txn_date }}
                                </div>
                            @endforeach
                        </div>
                    </div>
                    <input type="hidden" name="supplier_code" id="grn_supplier_code">

                    <div class="mb-3">
                        <label for="grn_password_field" class="form-label" style="font-weight: bold; color: black;">මුරපදය ඇතුලත් කරන්න</label>
                        <input type="password" name="report_password" id="grn_password_field" class="form-control" placeholder="මුරපදය">
                    </div>

                    <div id="grn_date_range_fields" style="display: none;">
                        <div class="mb-3">
                            <label for="grn_start_date" class="form-label" style="font-weight: bold; color: black;">ආරම්භ දිනය</label>
                            <input type="date" name="start_date" id="grn_start_date" class="form-control">
                        </div>
                        <div class="mb-3">
                            <label for="grn_end_date" class="form-label" style="font-weight: bold; color: black;">අවසන් දිනය</label>
                            <input type="date" name="end_date" id="grn_end_date" class="form-control">
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('report.email.grn-sales') }}" class="btn btn-info">
                        📧 Daily Email Report
                    </a>
                    <button type="submit" class="btn btn-primary w-100">වාර්තාව ලබාගන්න</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
    document.addEventListener("DOMContentLoaded", function () {
        const supplierCodeInput = document.getElementById('grn_supplier_code');
        const passwordField = document.getElementById('grn_password_field');
        const dateRangeFields = document.getElementById('grn_date_range_fields');
        const grnSaleReportModal = document.getElementById('grnSaleReportModal');
        const correctPassword = 'nethma123';
        
        // Custom search elements
        const grnSearchInput = document.getElementById('grnSearchInput');
        const grnCodeList = document.getElementById('grnCodeList');
        const grnCodeToSubmit = document.getElementById('grn_code_to_submit');
        const grnItems = Array.from(grnCodeList.children);

        // Function to handle the selection of a GRN item
        window.selectGrnCode = function(el) {
            // 1. Set the hidden input for form submission
            grnCodeToSubmit.value = el.dataset.code;
            
            // 2. Set the supplier code input
            supplierCodeInput.value = el.dataset.supplierCode || '';
            
            // 3. Update the search bar text to the selected GRN code
            grnSearchInput.value = el.dataset.code;
            
            // 4. Hide the dropdown list
            grnCodeList.style.display = 'none';
        };

        // Real-time search logic
        grnSearchInput.addEventListener('input', function() {
            const filter = this.value.trim().toLowerCase();

            // Show the list if there's text, otherwise hide it
            grnCodeList.style.display = filter.length > 0 ? 'block' : 'none';

            grnItems.forEach(item => {
                const grnCode = item.dataset.code.toLowerCase();
                const itemName = item.dataset.itemName.toLowerCase();

                // Check if GRN Code or Item Name starts with the filter
                if (grnCode.startsWith(filter) || itemName.startsWith(filter)) {
                    item.style.display = 'block';

                    // Optional: Reset item's inner HTML (important if you had previous highlighting)
                    const displayHtml = `<strong>${item.dataset.code}</strong> | ${item.dataset.supplierCode} | ${item.dataset.itemCode} | ${item.dataset.itemName} | ${item.dataset.packs} | ${item.dataset.grnNo} | ${item.dataset.txnDate}`;
                    item.innerHTML = displayHtml;
                } else {
                    item.style.display = 'none';
                }
            });
        });
        
        // Hide the list when clicking outside
        document.addEventListener('click', function(event) {
            if (!grnSearchInput.contains(event.target) && !grnCodeList.contains(event.target)) {
                grnCodeList.style.display = 'none';
            }
        });
        
        // Clear search input and selection when modal is shown
        grnSaleReportModal.addEventListener('shown.bs.modal', function () {
            grnSearchInput.value = '';
            grnCodeToSubmit.value = '';
            supplierCodeInput.value = '';
            grnCodeList.style.display = 'none';
            // Show all items initially (before any typing)
            grnItems.forEach(item => item.style.display = 'block');
        });


        // --- Existing Password & Date Range Logic ---

        // Show/hide date range fields based on password
        if(passwordField && dateRangeFields) {
            function checkPassword() {
                if(passwordField.value === correctPassword) {
                    dateRangeFields.style.display = 'block';
                } else {
                    dateRangeFields.style.display = 'none';
                }
            }
            passwordField.addEventListener('input', checkPassword);
            checkPassword();
        }

        // Refresh page on modal close
        if(grnSaleReportModal) {
            grnSaleReportModal.addEventListener('hidden.bs.modal', function () {
                window.location.reload();
            });
        }
    });
</script>