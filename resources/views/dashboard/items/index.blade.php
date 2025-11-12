@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99;
    }

    .custom-card {
        background-color: #006400 !important;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 24px;
    }

    .table thead th {
        background-color: #e6f0ff;
        color: #003366;
        text-align: center;
    }

    .table tbody td {
        vertical-align: middle;
        text-align: center;
    }

    .btn-sm {
        font-size: 0.875rem;
        padding: 6px 12px;
    }

    .table-hover tbody tr:hover {
        background-color: #f1f5ff;
    }

    .btn-success {
        background-color: #198754;
    }

    #itemSearch {
        max-width: 300px;
        margin-bottom: 15px;
    }
</style>

<div class="container-fluid mt-5">
    <div class="custom-card">
        <h2 class="mb-4 text-center text-primary">භාණ්ඩ ලැයිස්තුව (Items List)</h2>

        <div class="d-flex justify-content-between mb-3">
            <a href="{{ route('items.create') }}" class="btn btn-success">
                + නව භාණ්ඩයක් එකතු කරන්න
            </a>
            <input type="text" id="itemSearch" class="form-control form-control-sm" placeholder="අංකය හෝ වර්ගය අනුව සොයන්න" style="text-transform: uppercase;">
        </div>

        @if(session('success'))
            <div class="alert alert-success text-center">{{ session('success') }}</div>
        @endif

        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover align-middle" id="itemsTable">
                <thead>
                    <tr>
                        <th>ක අංකය</th>
                        <th>වර්ගය</th>
                        <th>මිලදි ගැනීමේ අගය</th>
                        <th>මල්ලක කුලිය</th>
                        <th>මෙහෙයුම්</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                    <tr>
                        <td style="text-transform: uppercase;">{{ $item->no }}</td>
                        <td>{{ $item->type }}</td>
                        <td>{{ number_format($item->pack_cost, 2) }}</td>
                        <td>{{ number_format($item->pack_due, 2) }}</td>
                          <td>
    <a href="{{ route('items.edit', $item->id) }}" 
       class="btn btn-primary btn-sm"
       @if(Auth::user()->role === 'Level2') onclick="return false;" style="pointer-events: none; opacity: 0.6;" @endif>
       යාවත්කාලීන
    </a>

    <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="d-inline">
        @csrf
        @method('DELETE')
        <button class="btn btn-danger btn-sm"
                onclick="return confirm('ඔබට මෙම භාණ්ඩය මකන්න අවශ්‍යද?')"
                @if(Auth::user()->role === 'Level2') disabled style="opacity: 0.6; cursor: not-allowed;" @endif>
            මකන්න
        </button>
    </form>
</td>
                    </tr>
                    @endforeach

                    @if($items->isEmpty())
                        <tr>
                            <td colspan="5" class="text-center text-muted">භාණ්ඩ නොමැත</td>
                        </tr>
                    @endif
                </tbody>
            </table>
            <a href="{{ route('items.export.excel') }}" class="btn btn-success">📥 Excel</a>
<a href="{{ route('items.export.pdf') }}" class="btn btn-danger">📥 PDF</a>
        </div>
    </div>
</div>

<script>
    const searchInput = document.getElementById('itemSearch');
    const table = document.getElementById('itemsTable').getElementsByTagName('tbody')[0];

    searchInput.addEventListener('keyup', function() {
        const filter = searchInput.value.toLowerCase();

        Array.from(table.getElementsByTagName('tr')).forEach(row => {
            const no = row.cells[0].textContent.toLowerCase();
            const type = row.cells[1].textContent.toLowerCase();

            // Only show rows where no OR type starts with the filter
            if(no.startsWith(filter) || type.startsWith(filter)) {
                row.style.display = '';
            } else {
                row.style.display = 'none';
            }
        });
    });
</script>

@endsection
