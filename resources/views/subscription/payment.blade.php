@extends('layouts.app')

@section('title', 'Subscription Payment')
@section('page-heading', 'Subscription & Payment')

@section('content')
<div class="container-fluid px-0">
    <div class="mb-4">
        <div class="text-uppercase small fw-bold text-success mb-2">DY Patil Hospital · OT Duty</div>
        <h1 class="h3 mb-1">{{ $hasAccess ? 'Trial and payment details' : 'Renew access' }}</h1>
        <p class="text-muted mb-0">
            @if ($hasAccess)
                This system is available during its free trial. Payment details are available here for renewal.
            @else
                The free trial or paid period has ended. Submit your payment reference to request access renewal.
            @endif
        </p>
    </div>

    @if (session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if ($errors->any())
        <div class="alert alert-danger">{{ $errors->first() }}</div>
    @endif

    <div class="row g-4">
        <div class="col-xl-7">
            <section class="card border-0 shadow-sm h-100">
                <div class="card-header bg-white py-3"><h2 class="h6 mb-0">Payment details</h2></div>
                <div class="card-body p-4">
                    @if ($settings->qr_code_path)
                        <div class="mb-4">
                            <div class="small text-muted fw-semibold mb-2">SCAN TO PAY</div>
                            <img class="subscription-qr" src="{{ url('/storage/'.$settings->qr_code_path) }}" alt="Payment QR code">
                        </div>
                    @elseif ($settings->upi_id)
                        <div class="mb-4">
                            <div class="small text-muted fw-semibold mb-2">PAY WITH UPI</div>
                            <p class="mb-2">UPI ID: <strong>{{ $settings->upi_id }}</strong></p>
                            <a class="btn btn-outline-primary" href="upi://pay?pa={{ urlencode($settings->upi_id) }}&amp;pn={{ urlencode('NBUNIFY PRIVATE LIMITED') }}&amp;cu=INR">Open UPI app</a>
                        </div>
                    @endif

                    @if ($settings->bank_details)
                        <div class="mb-4">
                            <div class="small text-muted fw-semibold mb-2">BANK / UPI DETAILS</div>
                            <div class="payment-copy">{{ $settings->bank_details }}</div>
                        </div>
                    @endif

                    @if ($settings->payment_instructions)
                        <div>
                            <div class="small text-muted fw-semibold mb-2">INSTRUCTIONS</div>
                            <div class="payment-copy">{{ $settings->payment_instructions }}</div>
                        </div>
                    @endif

                    @unless ($settings->qr_code_path || $settings->bank_details || $settings->upi_id)
                        <p class="text-muted mb-0">Payment details have not been configured yet. Please contact the system administrator.</p>
                    @endunless
                </div>
            </section>
        </div>

        <div class="col-xl-5">
            <section class="card border-0 shadow-sm mb-4">
                <div class="card-body p-4">
                    <div class="d-flex align-items-start gap-3">
                        <span class="badge {{ $hasAccess ? 'text-bg-success' : 'text-bg-danger' }}">{{ $hasAccess ? 'Active' : 'Expired' }}</span>
                        <div>
                            <div class="small text-muted">ACCESS STATUS</div>
                            <h2 class="h5 mb-1">{{ $hasAccess ? 'Access available' : 'Renewal required' }}</h2>
                            <p class="text-muted small mb-0">
                                @if ($hasAccess)
                                    Available through {{ $accessEndsAt->format('d M Y, h:i A') }}
                                @else
                                    Expired {{ $accessEndsAt->format('d M Y, h:i A') }}
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </section>

            <section class="card border-0 shadow-sm">
                <div class="card-header bg-white py-3"><h2 class="h6 mb-0">I have made a payment</h2></div>
                <div class="card-body p-4">
                    <p class="small text-muted">Enter the payment reference. A superadmin will verify it and activate one month of access.</p>
                    <form method="POST" action="{{ route('subscription.payment.submit') }}" enctype="multipart/form-data">
                        @csrf
                        <div class="mb-3">
                            <label for="payer_name" class="form-label">Name</label>
                            <input id="payer_name" name="payer_name" class="form-control" value="{{ old('payer_name', auth()->user()?->name) }}" required maxlength="150">
                        </div>
                        <div class="mb-3">
                            <label for="payer_email" class="form-label">Email</label>
                            <input id="payer_email" type="email" name="payer_email" class="form-control" value="{{ old('payer_email', auth()->user()?->email) }}" required maxlength="255">
                        </div>
                        <div class="mb-3">
                            <label for="reference" class="form-label">Transaction reference</label>
                            <input id="reference" name="reference" class="form-control" value="{{ old('reference') }}" required maxlength="120" autocomplete="off">
                        </div>
                        <div class="mb-3">
                            <label for="payment_screenshot" class="form-label">Payment screenshot</label>
                            <input id="payment_screenshot" type="file" name="payment_screenshot" class="form-control" accept="image/jpeg,image/png,image/webp" required>
                            <div class="form-text">JPG, PNG, or WebP, maximum 5 MB. Only superadmin can view it.</div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Submit payment reference</button>
                    </form>
                </div>
            </section>
        </div>
    </div>
</div>
@endsection