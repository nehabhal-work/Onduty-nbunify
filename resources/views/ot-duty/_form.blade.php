@php
    $isEdit = isset($otDuty);
    $currentSection = old('section', $otDuty->section ?? '2nd floor');
    $currentSection = [
        '2nd floor section' => '2nd floor',
        '4th floor section' => '4th floor',
        'LR section' => 'LR OT',
        'Recovery section' => 'Recovery',
        'Scope OT section' => 'Scope OT',
    ][$currentSection] ?? $currentSection;
@endphp

<div class="row g-3">

    <div class="col-md-3">
        <label class="form-label">Section <span class="text-danger">*</span></label>
        <select name="section" id="duty-section" class="form-select @error('section') is-invalid @enderror" required>
            @foreach ($sections as $section)
                <option value="{{ $section }}" @selected($currentSection === $section)>
                    {{ $section }}
                </option>
            @endforeach
        </select>
        @error('section')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label">Sister Name <span class="text-danger">*</span></label>
            @can('manage-staff-directory')<a class="small text-decoration-none mb-2" href="{{ route('staff-directory.create', ['type' => 'sister']) }}">Manage</a>@endcan
        </div>
        <select name="sister_name" class="form-select @error('sister_name') is-invalid @enderror" required>
            <option value="">Select Sister</option>
            @foreach ($sisters as $sister)
                <option value="{{ $sister }}" @selected(old('sister_name', $otDuty->sister_name ?? '') === $sister)>
                    {{ $sister }}
                </option>
            @endforeach
        </select>
        @error('sister_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3" data-duty-field="technician_name">
        <div class="d-flex justify-content-between align-items-center">
            <label class="form-label">Technician Name <span class="text-danger">*</span></label>
            @can('manage-staff-directory')<a class="small text-decoration-none mb-2" href="{{ route('staff-directory.create', ['type' => 'technician']) }}">Manage</a>@endcan
        </div>
        <select name="technician_name" class="form-select @error('technician_name') is-invalid @enderror" required>
            <option value="">Select Technician</option>
            @foreach ($technicians as $technician)
                <option value="{{ $technician }}" @selected(old('technician_name', $otDuty->technician_name ?? '') === $technician)>
                    {{ $technician }}
                </option>
            @endforeach
        </select>
        @error('technician_name')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Date &amp; Time <span class="text-danger">*</span></label>
        <input type="datetime-local" name="date_time"
            value="{{ old('date_time', isset($otDuty) && $otDuty->date_time ? $otDuty->date_time->format('Y-m-d\TH:i') : '') }}"
            class="form-control @error('date_time') is-invalid @enderror" required>
        @error('date_time')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3" data-duty-field="ot_no">
        <label class="form-label">OT No <span class="text-danger">*</span></label>
        <select name="ot_no" class="form-select @error('ot_no') is-invalid @enderror" required>
            <option value="">Select OT</option>
            @foreach ($otNumbers as $ot)
                <option value="{{ $ot }}"
                    @if (!in_array($ot, [17, 18, 19, 20, 21], true)) data-not-fourth-floor="true" @endif
                    @if (in_array($ot, [17, 18, 19, 20, 21], true)) data-not-second-floor="true" @endif
                    @selected((string) old('ot_no', $otDuty->ot_no ?? '') === (string) $ot)>
                    OT {{ $ot }}
                </option>
            @endforeach
        </select>
        @error('ot_no')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Shift <span class="text-danger">*</span></label>
        <select name="shift" class="form-select @error('shift') is-invalid @enderror" required>
            <option value="">Select Shift</option>
            @foreach ($shifts as $shift)
                <option value="{{ $shift }}" @selected(old('shift', $otDuty->shift ?? '') === $shift)>
                    {{ $shift }}
                </option>
            @endforeach
        </select>
        @error('shift')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3" data-duty-field="department">
        <label class="form-label">Department <span class="text-danger">*</span></label>
        <select name="department" class="form-select @error('department') is-invalid @enderror" required>
            <option value="">Select Department</option>
            @foreach ($departments as $department)
                <option value="{{ $department }}" @selected(old('department', $otDuty->department ?? '') === $department)>
                    {{ $department }}
                </option>
            @endforeach
        </select>
        @error('department')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3" data-duty-field="unit_no">
        <label class="form-label">Unit No <span class="text-danger">*</span></label>
        <select name="unit_no" class="form-select @error('unit_no') is-invalid @enderror" required>
            <option value="">Select Unit</option>
            @foreach ($units as $unit)
                <option value="{{ $unit }}" @selected((string) old('unit_no', $otDuty->unit_no ?? '') === (string) $unit)>
                    Unit {{ $unit }}
                </option>
            @endforeach
        </select>
        @error('unit_no')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12" data-duty-field="surgery">
        <label class="form-label">Surgery</label>
        <input type="text" name="surgery" value="{{ old('surgery', $otDuty->surgery ?? '') }}"
            class="form-control @error('surgery') is-invalid @enderror" placeholder="Enter surgery name">
        @error('surgery')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-12">
        <label class="form-label">Remarks</label>
        <textarea name="remarks" rows="4" class="form-control @error('remarks') is-invalid @enderror"
            placeholder="Enter remarks">{{ old('remarks', $otDuty->remarks ?? '') }}</textarea>
        @error('remarks')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

</div>
