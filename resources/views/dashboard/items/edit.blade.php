@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<style>
    body {
        background-color: #99ff99;
    }

    .form-card {
        background-color: #006400 !important;
        border-radius: 12px;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        padding: 24px;
        max-width: 600px;
        margin: 40px auto;
    }

    .form-label {
        font-weight: 700; /* Bold */
        color: #000000;   /* Bright black */
    }

    .form-control:focus {
        border-color: #4f46e5;
        box-shadow: 0 0 0 0.2rem rgba(79, 70, 229, 0.25);
    }

    .btn-success {
        background-color: #198754;
        border-color: #198754;
    }

    .btn-success:hover {
        background-color: #157347;
        border-color: #157347;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
</style>

<div class="form-card">
    <h3 class="mb-4 text-center text-primary">අයිතමය සංස්කරණය කරන්න (Edit Item)</h3>

    <form action="{{ route('items.update', $item->id) }}" method="POST">
        @csrf
        @method('PUT')

        <div class="mb-3">
            <label for="no" class="form-label">අංකය</label>
            <input type="text" name="no" id="no" class="form-control" value="{{ old('no', $item->no) }}" required>
        </div>

        <div class="mb-3">
            <label for="type" class="form-label">වර්ගය</label>
            <input type="text" name="type" id="type" class="form-control" value="{{ old('type', $item->type) }}" required>
        </div>

        <div class="mb-3">
            <label for="pack_cost" class="form-label">මිලදි ගැනීමේ අගය</label>
            <input type="number" name="pack_cost" id="pack_cost" step="0.01" class="form-control" value="{{ old('pack_cost', $item->pack_cost) }}" required>
        </div>

        <div class="mb-3">
            <label for="pack_due" class="form-label">මල්ලක කුලිය</label>
            <input type="number" name="pack_due" id="pack_due" step="0.01" class="form-control" value="{{ old('pack_due', $item->pack_due) }}" required>
        </div>

        <div class="d-flex justify-content-center mt-4">
            <button type="submit" class="btn btn-success px-4">
                <i class="material-icons align-middle me-1">edit</i>සංස්කරණය කරන්න
            </button>
            <a href="{{ route('items.index') }}" class="btn btn-secondary px-4 ms-2">
                <i class="material-icons align-middle me-1">cancel</i>අවලංගු කරන්න
            </a>
        </div>
    </form>
</div>
@endsection
