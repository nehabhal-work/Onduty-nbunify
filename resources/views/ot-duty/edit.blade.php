@extends('layouts.app')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1">Edit OT Duty Assignment</h4>
            <p class="text-muted mb-0">Update the selected OT assignment.</p>
        </div>

        <a href="{{ route('ot-duty.index') }}" class="btn btn-outline-secondary">
            Back
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="mb-0">OT Duty Details</h5>
        </div>

        <div class="card-body">
            <form action="{{ route('ot-duty.update', $otDuty) }}" method="POST" class="ot-duty-assignment-form">
                @csrf
                @method('PUT')

                @include('ot-duty._form', ['otDuty' => $otDuty])

                <div class="border-top mt-4 pt-3">
                    <button type="submit" class="btn btn-primary">
                        Update Assignment
                    </button>

                    <a href="{{ route('ot-duty.index') }}" class="btn btn-outline-secondary ms-2">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
