@extends('layouts.app')

@section('content')
    <style>
        .ot-duty-table th {
            white-space: nowrap;
            font-size: 13px;
        }

        .ot-duty-table td {
            vertical-align: middle;
            font-size: 14px;
        }

        .action-btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        .filter-label {
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 5px;
        }
    </style>

    <div class="container-fluid py-4">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 class="mb-1">OT Duty Assignments</h4>
                <p class="text-muted mb-0">
                    Manage Sister and Technician OT assignments teasting.
                </p>
            </div>

            @can('manage-ot-duty')
                <div class="d-flex flex-wrap gap-2">
                    <a href="{{ route('staff-directory.index') }}" class="btn btn-outline-secondary">
                        Manage staff
                    </a>
                    <a href="{{ route('ot-duty.create') }}" class="btn btn-primary">
                        Add Assignment
                    </a>
                </div>
            @endcan
        </div>

        @if (session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0 mb-4">
            <div class="card-body">

                <form method="GET" action="{{ route('ot-duty.index') }}" class="ot-duty-filter-form">
                    <div class="row g-3">

                        <div class="col-xl-2 col-md-4">
                            <label class="filter-label">Sister</label>
                            <select name="sister_name" class="form-select form-select-sm">
                                <option value="">All Sisters</option>
                                @foreach ($sisters as $sister)
                                    <option value="{{ $sister }}" @selected(request('sister_name') === $sister)>
                                        {{ $sister }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-2 col-md-4">
                            <label class="filter-label">Technician</label>
                            <select name="technician_name" class="form-select form-select-sm">
                                <option value="">All Technicians</option>
                                @foreach ($technicians as $technician)
                                    <option value="{{ $technician }}" @selected(request('technician_name') === $technician)>
                                        {{ $technician }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-1 col-md-4">
                            <label class="filter-label">OT No</label>
                            <select name="ot_no" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($otNumbers as $ot)
                                    <option value="{{ $ot }}" @selected((string) request('ot_no') === (string) $ot)>
                                        OT {{ $ot }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-2 col-md-4">
                            <label class="filter-label">Shift</label>
                            <select name="shift" class="form-select form-select-sm">
                                <option value="">All Shifts</option>
                                @foreach ($shifts as $shift)
                                    <option value="{{ $shift }}" @selected(request('shift') === $shift)>
                                        {{ $shift }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-2 col-md-4">
                            <label class="filter-label">Department</label>
                            <select name="department" class="form-select form-select-sm">
                                <option value="">All Departments</option>
                                @foreach ($departments as $department)
                                    <option value="{{ $department }}" @selected(request('department') === $department)>
                                        {{ $department }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-1 col-md-4">
                            <label class="filter-label">Unit</label>
                            <select name="unit_no" class="form-select form-select-sm">
                                <option value="">All</option>
                                @foreach ($units as $unit)
                                    <option value="{{ $unit }}" @selected((string) request('unit_no') === (string) $unit)>
                                        {{ $unit }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-xl-2 col-md-12 d-flex align-items-end gap-2">
                            <button type="submit" class="btn btn-primary btn-sm flex-grow-1">
                                Search
                            </button>

                            <a href="{{ route('ot-duty.index') }}" class="btn btn-outline-secondary btn-sm">
                                Reset
                            </a>
                        </div>

                    </div>
                </form>

            </div>
        </div>

        <div class="card shadow-sm border-0">

            <div class="card-header bg-white py-3">
                <div class="d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">OT Duty List</h5>
                    <span class="badge bg-primary">{{ $otDuties->total() }} Records</span>
                </div>
            </div>

            <div class="card-body p-0">

                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0 ot-duty-table">

                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Sister Name</th>
                                <th>Technician Name</th>
                                <th>Date &amp; Time</th>
                                <th>OT No</th>
                                <th>Shift</th>
                                <th>Department</th>
                                <th>Unit</th>
                                <th>Surgery</th>
                                <th>Remarks</th>
                                @auth
                                    <th class="text-center">Action</th>
                                @endauth
                            </tr>
                        </thead>

                        <tbody>

                            @forelse($otDuties as $otDuty)
                                <tr>

                                    <td>{{ $otDuties->firstItem() + $loop->index }}</td>

                                    <td>
                                        <strong>{{ $otDuty->sister_name }}</strong>
                                    </td>

                                    <td>{{ $otDuty->technician_name ?: '-' }}</td>

                                    <td>{{ $otDuty->date_time?->format('d M Y, h:i A') ?? '-' }}</td>

                                    <td>
                                        <span class="badge bg-light text-dark border">
                                            OT {{ $otDuty->ot_no }}
                                        </span>
                                    </td>

                                    <td>
                                        @php
                                            $shiftClass = match ($otDuty->shift) {
                                                'Morning' => 'bg-success',
                                                'Evening' => 'bg-warning text-dark',
                                                'Night' => 'bg-info text-dark',
                                                default => 'bg-secondary',
                                            };
                                        @endphp

                                        <span class="badge {{ $shiftClass }}">
                                            {{ $otDuty->shift }}
                                        </span>
                                    </td>

                                    <td>{{ $otDuty->department }}</td>

                                    <td>Unit {{ $otDuty->unit_no }}</td>

                                    <td>{{ $otDuty->surgery ?: '-' }}</td>

                                    <td>{{ $otDuty->remarks ?: '-' }}</td>

                                    @auth
                                        <td class="text-center text-nowrap">

                                            <a href="{{ route('ot-duty.show', $otDuty) }}"
                                                class="btn btn-sm btn-outline-info action-btn" title="View">
                                                View
                                            </a>

                                            <a href="{{ route('ot-duty.edit', $otDuty) }}"
                                                class="btn btn-sm btn-outline-primary action-btn" title="Edit">
                                                Edit
                                            </a>

                                            <form action="{{ route('ot-duty.destroy', $otDuty) }}" method="POST"
                                                class="d-inline"
                                                onsubmit="return confirm('Are you sure you want to delete this OT assignment?');">
                                                @csrf
                                                @method('DELETE')

                                                <button type="submit" class="btn btn-sm btn-outline-danger action-btn"
                                                    title="Delete">
                                                    Delete
                                                </button>
                                            </form>

                                        </td>
                                    @endauth

                                </tr>

                            @empty

                                <tr>
                                    <td colspan="{{ auth()->check() ? 11 : 10 }}" class="text-center py-5 text-muted">
                                        No OT duty assignments found.
                                    </td>
                                </tr>
                            @endforelse

                        </tbody>

                    </table>
                </div>

            </div>

            @if ($otDuties->hasPages())
                <div class="card-footer bg-white">
                    {{ $otDuties->links() }}
                </div>
            @endif

        </div>

    </div>
@endsection
