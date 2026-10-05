@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-heading', 'Dashboard')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <div class="text-uppercase small fw-bold text-success mb-2">Staff workspace</div>
            <h1 class="h3 mb-1">Good {{ now()->hour < 12 ? 'morning' : (now()->hour < 18 ? 'afternoon' : 'evening') }}, {{ auth()->user()->name }}</h1>
            <p class="text-muted mb-0">Operation theatre duty overview for {{ now()->format('l, d F Y') }}.</p>
        </div>
        @can('manage-ot-duty')
            <a href="{{ route('ot-duty.create') }}" class="btn btn-primary">
                New assignment
            </a>
        @endcan
    </div>

    <div class="row g-3 mb-4">
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="dashboard-metric-icon rounded-2 text-success bg-success-subtle" aria-hidden="true">Today</div>
                <div><div class="text-muted small">Assignments today</div><div class="fs-3 fw-semibold">{{ $todayAssignments }}</div></div>
            </div></div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="dashboard-metric-icon rounded-2 text-warning bg-warning-subtle" aria-hidden="true">Soon</div>
                <div><div class="text-muted small">Upcoming duties</div><div class="fs-3 fw-semibold">{{ $upcomingCount }}</div></div>
            </div></div>
        </div>
        <div class="col-sm-6 col-xl-4">
            <div class="card border-0 shadow-sm h-100"><div class="card-body d-flex align-items-center gap-3 p-4">
                <div class="dashboard-metric-icon rounded-2 text-danger bg-danger-subtle" aria-hidden="true">All</div>
                <div><div class="text-muted small">All assignments</div><div class="fs-3 fw-semibold">{{ $totalAssignments }}</div></div>
            </div></div>
        </div>
    </div>

    <div class="row g-3">
        <div class="col-xl-8">
            <section class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 d-flex justify-content-between align-items-center px-4 pt-4">
                    <div><h2 class="h6 mb-1">Upcoming assignments</h2><p class="small text-muted mb-0">Next scheduled OT duties</p></div>
                    <a href="{{ route('ot-duty.index') }}" class="btn btn-sm btn-outline-secondary">View all</a>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr class="small text-muted"><th class="ps-4">Date &amp; time</th><th>OT / Section</th><th>Sister</th><th>Technician</th><th>Shift</th></tr></thead>
                        <tbody>
                            @forelse ($upcomingAssignments as $assignment)
                                <tr>
                                    <td class="ps-4"><div class="fw-semibold small">{{ $assignment->date_time?->format('d M Y') }}</div><div class="text-muted small">{{ $assignment->date_time?->format('h:i A') }}</div></td>
                                    <td><div class="fw-semibold small">{{ $assignment->ot_no ? 'OT '.$assignment->ot_no : $assignment->section }}</div><div class="text-muted small">{{ $assignment->department ?: $assignment->section }}</div></td>
                                    <td class="small">{{ $assignment->sister_name }}</td>
                                    <td class="small">{{ $assignment->technician_name }}</td>
                                    <td><span class="badge text-bg-light border">{{ $assignment->shift }}</span></td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="text-center text-muted py-5">No upcoming assignments.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </section>
        </div>
        <div class="col-xl-4">
            <section class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white border-0 px-4 pt-4"><h2 class="h6 mb-1">Assignments by section</h2><p class="small text-muted mb-0">All scheduled duties</p></div>
                <div class="card-body px-4">
                    @forelse ($sectionCounts as $section)
                        <div class="d-flex justify-content-between align-items-center py-3 border-bottom">
                            <span class="small">{{ $section->section }}</span>
                            <span class="badge rounded-pill text-bg-light border">{{ $section->total }}</span>
                        </div>
                    @empty
                        <p class="text-muted small mb-0 py-3">No assignments have been added yet.</p>
                    @endforelse
                </div>
            </section>
        </div>
    </div>
</div>
@endsection