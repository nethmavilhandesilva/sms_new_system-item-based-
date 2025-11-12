@extends('layouts.app')

@section('content')
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

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

    .form-label {
        font-weight: 700;  /* Bold */
        color: #000000;    /* Bright Black */
    }

    .btn-success {
        background-color: #198754;
        border-color: #198754;
    }

    .btn-secondary {
        background-color: #6c757d;
        border-color: #6c757d;
    }
</style>

<div class="container-fluid mt-5">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="custom-card">
                <h2 class="text-primary mb-4">නව භාණ්ඩය එක් කරන්න (Add New Item)</h2>

                <form action="{{ route('items.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label for="no" class="form-label">අංකය</label>
                        <input type="text" id="no" name="no" class="form-control" required style="text-transform: uppercase;">
                    </div>
                    <div class="mb-3">
                        <label for="type" class="form-label">වර්ගය</label>
                        <input type="text" id="type" name="type" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="pack_cost" class="form-label">මිලදි ගැනීමේ අගය</label>
                        <input type="number" id="pack_cost" name="pack_cost" step="0.01" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="pack_due" class="form-label">මල්ලක කුලිය</label>
                        <input type="number" id="pack_due" name="pack_due" step="0.01" class="form-control" required>
                    </div>
                    <div class="d-flex justify-content-center mt-4">
                        <button type="submit" class="btn btn-success me-2">
                            <i class="material-icons align-middle me-1">add_circle_outline</i>එක් කරන්න
                        </button>
                        <a href="{{ route('items.index') }}" class="btn btn-secondary">
                            <i class="material-icons align-middle me-1">cancel</i>අවලංගු කරන්න
                        </a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
