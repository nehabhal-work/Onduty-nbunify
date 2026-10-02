@extends('layouts.app')

@section('title', 'Assignment Details')
@section('page-heading', 'OT Duty Details')

@section('content')
<div class="container-fluid py-4">

    <div class="d-flex flex-wrap justify-content-between align-items-center gap-3 mb-4">
        <div>
            <h1 class="h4 mb-1">OT Duty Assignment</h1>
            <p class="text-muted mb-0">Assignment details and staff allocation.</p>
        </div>

        <div class="d-flex gap-2">
            @can('manage-ot-duty')
                <a href="{{ route('ot-duty.edit', $otDuty) }}" class="btn btn-primary">
                    <i class="bi bi-pencil me-1"></i> Edit assignment
                </a>
            @endcan
            <a href="{{ route('ot-duty.index') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to list
            </a>
        </div>
    </div>

    <div class="row justify-content-center">
        <div class="col-md-6">
            <section class="card shadow-sm border-0">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h2 class="h6 mb-0">Assignment summary</h2>
                    <span class="badge bg-primary-subtle text-primary-emphasis">{{ $otDuty->section }}</span>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-borderless align-middle mb-0">
                            <tbody>
                                <tr><th class="ps-4 text-muted fw-medium" scope="row">Sister</th><td class="pe-4 fw-semibold">{{ $otDuty->sister_name }}</td></tr>
                                <tr><th class="ps-4 text-muted fw-medium" scope="row">Technician</th><td class="pe-4 fw-semibold">{{ $otDuty->technician_name ?: '-' }}</td></tr>
                                <tr><th class="ps-4 text-muted fw-medium" scope="row">Date &amp; time</th><td class="pe-4">{{ $otDuty->date_time?->format('d M Y, h:i A') ?? '-' }}</td></tr>
                                <tr><th class="ps-4 text-muted fw-medium" scope="row">Shift</th><td class="pe-4">{{ $otDuty->shift }}</td></tr>
                                <tr><th class="ps-4 text-muted fw-medium" scope="row">OT number</th><td class="pe-4">{{ $otDuty->ot_no ? 'OT '.$otDuty->ot_no : '-' }}</td></tr>
                                <tr><th class="ps-4 text-muted fw-medium" scope="row">Department</th><td class="pe-4">{{ $otDuty->department ?: '-' }}</td></tr>
                                <tr><th class="ps-4 text-muted fw-medium" scope="row">Unit</th><td class="pe-4">{{ $otDuty->unit_no ? 'Unit '.$otDuty->unit_no : '-' }}</td></tr>
                                <tr><th class="ps-4 text-muted fw-medium" scope="row">Surgery</th><td class="pe-4">{{ $otDuty->surgery ?: '-' }}</td></tr>
                                <tr><th class="ps-4 text-muted fw-medium" scope="row">Remarks</th><td class="pe-4 text-break">{{ $otDuty->remarks ?: '-' }}</td></tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
