<!-- Supplier Select Modal (shared by all reports) -->
<div class="modal fade" id="supplierSelectModal" tabindex="-1" aria-labelledby="supplierSelectModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <form method="GET" id="supplierSelectForm" action="">
            <div class="modal-content">
                <div class="modal-header" style="background-color: #004d00; color: white;">
                    <h5 class="modal-title" id="supplierSelectModalLabel">Select Supplier</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <label for="supplier_code" class="form-label">Supplier Code</label>
                    <select class="form-select" name="supplier_code" id="supplier_code" required>
                        <option value="">-- All Suppliers --</option>
                        <option value="L" {{ request('supplier_code') == 'L' ? 'selected' : '' }}>L</option>
                        <option value="A" {{ request('supplier_code') == 'A' ? 'selected' : '' }}>A</option>
                    </select>
                </div>

                <div class="modal-footer">
                    <a href="{{ route('grn.sendEmail') }}" class="btn btn-info">📧 Daily Email Report</a>
                    <button type="submit" class="btn btn-success">Generate Report</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const modal = document.getElementById('supplierSelectModal');
    modal.addEventListener('show.bs.modal', function(event) {
        const button = event.relatedTarget; // Button that opened the modal
        const actionUrl = button.getAttribute('data-report-action'); // custom attribute
        document.getElementById('supplierSelectForm').action = actionUrl;
    });
});
</script>
