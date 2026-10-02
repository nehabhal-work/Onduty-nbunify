@extends('layouts.app')

@section('title', 'Subscription Settings')
@section('page-heading', 'Subscription Settings')

@section('content')
<div class="container-fluid px-0">
    <div class="d-flex flex-wrap justify-content-between align-items-end gap-3 mb-4">
        <div>
            <div class="text-uppercase small fw-bold text-success mb-2">Superadmin</div>
            <h1 class="h3 mb-1">Subscription settings</h1>
            <p class="text-muted mb-0">Manage the trial, payment instructions, and monthly access approvals.</p>
        </div>
        <span class="badge {{ $hasAccess ? 'text-bg-success' : 'text-bg-danger' }}">{{ $hasAccess ? 'System active' : 'Payment required' }}</span>
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
        <div class="col-xl-5">
            <section class="card border-0 shadow-sm mb-4">
                <div class="card-header bg-white py-3"><h2 class="h6 mb-0">Trial and access</h2></div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('superadmin.subscription-settings.trial.update') }}">
                        @csrf
                        @method('PUT')
                        <div class="row g-3 mb-3">
                            <div class="col-sm-6">
                                <label for="trial_start_date" class="form-label">Trial started</label>
                                <input id="trial_start_date" type="date" name="trial_start_date" class="form-control"
                                    value="{{ old('trial_start_date', $settings->trial_started_at?->format('Y-m-d')) }}"
                                    max="{{ now()->format('Y-m-d') }}" required>
                            </div>
                            <div class="col-sm-6">
                                <label for="trial_duration" class="form-label">Trial length</label>
                                <select id="trial_duration" name="trial_duration" class="form-select" required>
                                    <option value="15d" @selected(old('trial_duration', $settings->trial_duration_minutes ? '3m' : $settings->trial_duration_days.'d') === '15d')>15 days</option>
                                    <option value="30d" @selected(old('trial_duration', $settings->trial_duration_minutes ? '3m' : $settings->trial_duration_days.'d') === '30d')>30 days</option>
                                    @if (app()->environment(['local', 'testing']))
                                        <option value="3m" @selected(old('trial_duration', $settings->trial_duration_minutes ? '3m' : $settings->trial_duration_days.'d') === '3m')>3 minutes (testing only)</option>
                                    @endif
                                </select>
                            </div>
                        </div>
                        <p class="form-text mb-3">The 3-minute test starts immediately and clears any paid-through date. To test lockout after three minutes, check as a manager or guest; superadmins retain access.</p>
                        <button type="submit" class="btn btn-primary"><i class="bi bi-calendar-check me-1"></i> Save trial schedule</button>
                    </form>

                    <hr class="my-4">
                    <dl class="row mb-0 small">
                        <dt class="col-6 text-muted">Trial ends</dt><dd class="col-6 text-end">{{ $trialEndsAt->format('d M Y, h:i A') }}</dd>
                        <dt class="col-6 text-muted">Paid through</dt><dd class="col-6 text-end">{{ $settings->paid_until?->format('d M Y, h:i A') ?? 'No payment recorded' }}</dd>
                        <dt class="col-6 text-muted">Current access ends</dt><dd class="col-6 text-end fw-semibold">{{ $accessEndsAt->format('d M Y, h:i A') }}</dd>
                    </dl>
                </div>
            </section>

            <section class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h2 class="h6 mb-0">Trial and payment details</h2></div>
                <div class="card-body p-4">
                    <form method="POST" action="{{ route('superadmin.subscription-settings.update') }}" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="upi_id" class="form-label">UPI ID</label>
                            <input id="upi_id" name="upi_id" class="form-control" value="{{ old('upi_id', $settings->upi_id) }}" maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="bank_details" class="form-label">Bank / UPI details</label>
                            <textarea id="bank_details" name="bank_details" class="form-control" rows="4" maxlength="4000">{{ old('bank_details', $settings->bank_details) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="payment_instructions" class="form-label">Payment instructions</label>
                            <textarea id="payment_instructions" name="payment_instructions" class="form-control" rows="3" maxlength="4000">{{ old('payment_instructions', $settings->payment_instructions) }}</textarea>
                        </div>
                        <div class="mb-3">
                            <label for="qr_code" class="form-label">Payment QR code</label>
                            <input id="qr_code" type="file" name="qr_code" class="form-control" accept="image/png,image/jpeg,image/webp">
                            <div class="form-text">PNG, JPG, or WebP; maximum 2 MB.</div>
                        </div>
                        @if ($settings->qr_code_path)
                            <img class="subscription-qr mb-3" src="{{ url('/storage/'.$settings->qr_code_path) }}" alt="Current payment QR code">
                        @endif
                        <button type="submit" class="btn btn-primary"><i class="bi bi-save me-1"></i> Save settings</button>
                    </form>
                </div>
            </section>
        </div>

        <div class="col-xl-7">
            <section class="card border-0 shadow-sm">
                <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                    <h2 class="h6 mb-0">Payment requests</h2>
                    <span class="badge bg-primary">{{ $payments->total() }}</span>
                </div>
                <div class="table-responsive">
                    <table class="table align-middle mb-0">
                        <thead><tr><th class="ps-4">Payer</th><th>Reference</th><th>Proof</th><th>Submitted</th><th>Status</th><th class="text-end pe-4">Review</th></tr></thead>
                        <tbody>
                            @forelse ($payments as $payment)
                                <tr>
                                    <td class="ps-4"><div class="fw-semibold">{{ $payment->payer_name }}</div><div class="small text-muted">{{ $payment->payer_email }}</div></td>
                                    <td>{{ $payment->reference }}</td>
                                    <td><a href="{{ route('superadmin.subscription-payments.proof', $payment) }}" target="_blank" rel="noopener noreferrer">View screenshot</a></td>
                                    <td class="small">{{ $payment->created_at->format('d M Y, h:i A') }}</td>
                                    <td><span class="badge {{ $payment->status === 'pending' ? 'text-bg-warning' : ($payment->status === 'approved' ? 'text-bg-success' : 'text-bg-secondary') }}">{{ ucfirst($payment->status) }}</span></td>
                                    <td class="text-end text-nowrap pe-4">
                                        @if ($payment->status === 'pending')
                                            <form method="POST" action="{{ route('superadmin.subscription-payments.approve', $payment) }}" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-sm btn-success" type="submit" title="Approve and add one month">Approve</button>
                                            </form>
                                            <form method="POST" action="{{ route('superadmin.subscription-payments.reject', $payment) }}" class="d-inline">
                                                @csrf @method('PATCH')
                                                <button class="btn btn-sm btn-outline-danger" type="submit">Reject</button>
                                            </form>
                                        @else
                                            <span class="small text-muted">{{ $payment->reviewed_at?->format('d M Y') }}</span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="text-center text-muted py-5">No payment requests yet.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if ($payments->hasPages())
                    <div class="card-footer bg-white">{{ $payments->links() }}</div>
                @endif
            </section>
        </div>
    </div>
</div>
@endsection
