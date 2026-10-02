@extends('layouts.app')

@section('title', 'Staff Directory')
@section('page-heading', 'Staff Directory')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <div class="text-uppercase small fw-bold text-success mb-2">OT Duty Assignment</div>
            <h1 class="h3 mb-1">Sisters &amp; Technicians</h1>
            <p class="text-muted mb-0">Manage the staff names available in assignment forms.</p>
        </div>
        <div class="d-flex flex-wrap gap-2">
            <a href="{{ route('staff-directory.create', ['type' => 'sister']) }}" class="btn btn-primary"><i class="bi bi-plus-lg me-1"></i> Add Sister</a>
            <a href="{{ route('staff-directory.create', ['type' => 'technician']) }}" class="btn btn-outline-primary"><i class="bi bi-plus-lg me-1"></i> Add Technician</a>
        </div>
    </div>

    @if (session('success'))
        <div class="alert alert-success alert-dismissible fade show">
            {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        @foreach (['sister' => 'Sisters', 'technician' => 'Technicians'] as $type => $label)
            <div class="col-xl-6">
                <section class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                        <h2 class="h6 mb-0">{{ $label }}</h2>
                        <span class="badge bg-primary">{{ $members->get($type, collect())->count() }}</span>
                    </div>
                    <div class="table-responsive">
                        <table class="table align-middle mb-0">
                            <thead><tr><th class="ps-4">Name / Contact</th><th>Address</th><th class="text-end pe-4">Actions</th></tr></thead>
                            <tbody>
                                @forelse ($members->get($type, collect()) as $member)
                                    <tr>
                                        <td class="ps-4">
                                            <div class="fw-semibold">{{ $member->name }}</div>
                                            @if ($member->mobile)<div class="small text-muted">{{ $member->mobile }}</div>@endif
                                            @if ($member->email)<div class="small text-muted">{{ $member->email }}</div>@endif
                                        </td>
                                        <td class="small">{{ $member->address ?: '-' }}</td>
                                        <td class="text-end text-nowrap pe-4">
                                            <a href="{{ route('staff-directory.edit', $member) }}" class="btn btn-sm btn-outline-primary" aria-label="Edit {{ $member->name }}" title="Edit"><i class="bi bi-pencil"></i></a>
                                            <form action="{{ route('staff-directory.destroy', $member) }}" method="POST" class="d-inline" onsubmit="return confirm('Remove {{ $member->name }} from this staff directory?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-outline-danger" aria-label="Remove {{ $member->name }}" title="Remove"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="text-center text-muted py-5">No {{ strtolower($label) }} added.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </section>
            </div>
        @endforeach
    </div>
</div>
@endsection
