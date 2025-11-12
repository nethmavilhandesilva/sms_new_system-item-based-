@extends('layouts.app')

@section('content')
<style>
    body {
        background-color: #99ff99; /* Full page background */
    }

    /* Optional: keep your forms a little distinct */
    .bg-light {
        background-color: #ffffff !important;
    }
</style>
<div class="container mt-4">
    <h3 class="text-success mb-3">Edit Supplier</h3>

    <form action="{{ route('suppliers2.update', $supplier->id) }}" method="POST" class="border p-3 rounded bg-light">
        @csrf
        @method('PUT')

        <div class="row mb-3">
            <div class="col-md-4">
                <label for="supplier_code" class="form-label">Supplier Code</label>
                <input type="text" name="supplier_code" id="supplier_code" class="form-control text-uppercase"
                       value="{{ old('supplier_code', $supplier->supplier_code) }}" required>
            </div>
            <div class="col-md-4">
                <label for="supplier_name" class="form-label">Supplier Name</label>
                <input type="text" name="supplier_name" id="supplier_name" class="form-control"
                       value="{{ old('supplier_name', $supplier->supplier_name) }}" required>
            </div>
            <div class="col-md-4">
                <label for="total_amount" class="form-label">Total Amount</label>
                <input type="number" step="0.01" name="total_amount" id="total_amount" class="form-control"
                       value="{{ old('total_amount', $supplier->total_amount) }}" required>
            </div>
        </div>

        <div class="row mb-3">
            <div class="col-md-6">
                <label for="grn_id" class="form-label">Select GRN</label>
                <select name="grn_id" id="grn_id" class="form-select" required>
                    @foreach($grnOptions as $id => $grnNo)
                        <option value="{{ $id }}" {{ $supplier->grn_id == $id ? 'selected' : '' }}>
                            {{ $grnNo }}
                        </option>
                    @endforeach
                </select>
            </div>
        </div>

        <button type="submit" class="btn btn-success">Update Supplier</button>
        <a href="{{ route('suppliers2.index') }}" class="btn btn-secondary">Cancel</a>
    </form>
</div>
@endsection
