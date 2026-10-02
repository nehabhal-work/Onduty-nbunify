@extends('layouts.app')

@section('title', $staffMember ? 'Edit Staff Member' : 'Add Staff Member')
@section('page-heading', 'Staff Directory')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <div class="text-uppercase small fw-bold text-success mb-2">{{ ucfirst($type) }}</div>
            <h1 class="h3 mb-1">{{ $staffMember ? 'Edit staff member' : 'Add '.($type === 'sister' ? 'sister' : 'technician') }}</h1>
            <p class="text-muted mb-0">Only the name is required. Contact and address details are optional.</p>
        </div>
        <a href="{{ route('staff-directory.index') }}" class="btn btn-outline-secondary"><i class="bi bi-arrow-left me-1"></i> Back to staff</a>
    </div>

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row">
        <div class="col-xl-6 col-lg-8">
            <section class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h2 class="h6 mb-0">Staff details</h2></div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ $staffMember ? route('staff-directory.update', $staffMember) : route('staff-directory.store') }}">
                        @csrf
                        @if ($staffMember)
                            @method('PUT')
                        @else
                            <input type="hidden" name="type" value="{{ $type }}">
                        @endif

                        <div class="mb-3">
                            <label for="name" class="form-label">Name <span class="text-danger">*</span></label>
                            <input id="name" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $staffMember?->name) }}" maxlength="100" required autofocus>
                            @error('name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="mobile" class="form-label">Mobile</label>
                            <input id="mobile" name="mobile" type="tel" class="form-control @error('mobile') is-invalid @enderror" value="{{ old('mobile', $staffMember?->mobile) }}" maxlength="30" autocomplete="tel">
                            @error('mobile')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input id="email" name="email" type="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $staffMember?->email) }}" maxlength="255" autocomplete="email">
                            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="mb-4">
                            <label for="address" class="form-label">Address</label>
                            <textarea id="address" name="address" class="form-control @error('address') is-invalid @enderror" rows="3" maxlength="500">{{ old('address', $staffMember?->address) }}</textarea>
                            @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-check-lg me-1"></i> {{ $staffMember ? 'Save changes' : 'Add staff member' }}</button>
                        <a href="{{ route('staff-directory.index') }}" class="btn btn-outline-secondary ms-2">Cancel</a>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection
