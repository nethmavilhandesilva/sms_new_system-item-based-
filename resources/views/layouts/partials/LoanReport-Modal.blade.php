<div class="modal fade" id="reportLoanModal" tabindex="-1" aria-labelledby="reportLoanModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <form id="loanReportForm" action="{{ route('loan.report.results') }}" method="POST" target="_blank">
      @csrf
      <div class="modal-content" style="background-color: #99ff99;">
        <div class="modal-header">
          <h5 class="modal-title" id="reportLoanModalLabel">📄 ගැනුම්කරු ණය වාර්තාව</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>

        <div class="modal-body">
          <div class="mb-3">
            <label for="loanReport_password" class="form-label" style="font-weight: bold; color: black;">පස්වර්ඩ් ඇතුල් කරන්න</label>
            <input type="password" id="loanReport_password" name="password" class="form-control" placeholder="පස්වර්ඩ්">
          </div>

          <div class="mb-3">
            <label for="loanReport_customer_select" class="form-label" style="font-weight: bold; color: black;">ගැනුම්කරු තෝරන්න</label>
            <select id="loanReport_customer_select" class="form-select form-select-sm select2" name="customer_short_name">
              <option value="">-- ගැනුම්කරු --</option>
              @foreach ($customers as $customer)
                <option value="{{ $customer->short_name }}">
                  {{ $customer->short_name }}
                </option>
              @endforeach
            </select>
          </div>

          <div id="loanReport_date_range_container" style="display: none;">
            <div class="mb-3">
              <label for="loanReport_start_date" class="form-label" style="font-weight: bold; color: black;">ආරම්භ දිනය</label>
              <input type="date" id="loanReport_start_date" name="start_date" class="form-control">
            </div>

            <div class="mb-3">
              <label for="loanReport_end_date" class="form-label" style="font-weight: bold; color: black;">අවසන් දිනය</label>
              <input type="date" id="loanReport_end_date" name="end_date" class="form-control">
            </div>
          </div>
        </div>

        <div class="modal-footer">
          <a href="{{ route('report.loans.email-simple') }}" class="print-btn" style="text-decoration: none;">
            📧 Email Report
          </a>
          <button type="submit" class="btn btn-primary w-100">ඉදිරිපත් කරන්න</button>
        </div>
      </div>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>
document.addEventListener("DOMContentLoaded", function () {
    const customerSelect = document.getElementById('loanReport_customer_select');
    const passwordInput = document.getElementById('loanReport_password');
    const dateRangeContainer = document.getElementById('loanReport_date_range_container');
    const correctPassword = 'nethma123';
    const form = document.getElementById('loanReportForm');

    // Initialize Select2 inside modal with custom matcher
    $(customerSelect).select2({
        dropdownParent: $('#reportLoanModal'),
        matcher: function(params, data) {
            if ($.trim(params.term) === '') {
                return data; // Show all options if search is empty
            }
            if (data.id) { // data.id = short_name
                const term = params.term.toLowerCase();
                const shortName = data.id.toLowerCase();
                if (shortName.startsWith(term)) {
                    return data; // Match first letters of short_name
                }
            }
            return null; // No match
        }
    });

    // Password show/hide date range
    passwordInput.addEventListener('input', function () {
        if (passwordInput.value === correctPassword) {
            dateRangeContainer.style.display = 'block';
        } else {
            dateRangeContainer.style.display = 'none';
            document.getElementById('loanReport_start_date').value = '';
            document.getElementById('loanReport_end_date').value = '';
        }
    });

    // Prevent form submit if customer not selected
    form.addEventListener('submit', function (e) {
        if (customerSelect.value === "") {
            e.preventDefault();
            alert("කරුණාකර ගැනුම්කරු තෝරන්න"); // Sinhala alert message
        }
    });
});
</script>


