@extends('layouts.app')

@section('title', 'Welcome')
@section('page-heading', 'Welcome')

@section('content')
    <div class="container py-5">
        <div class="card shadow-sm">
            <div class="card-body p-4">
                <h1 class="h3">OT Duty Assignment</h1>
                <p class="text-body-secondary">Manage Sister and Technician OT assignments.</p>
                <a href="{{ route('ot-duty.index') }}" class="btn btn-primary">View assignments</a>
            </div>
        </div>
    </div>
@endsection
