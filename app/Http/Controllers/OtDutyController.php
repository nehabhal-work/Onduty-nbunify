<?php

namespace App\Http\Controllers;

use App\Http\Requests\OtDutyRequest;
use App\Models\OtDuty;
use App\Models\StaffMember;
use App\Services\OtDutyService;
use App\Support\OtDutyOptions;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class OtDutyController extends Controller
{
    public function __construct(
        private readonly OtDutyService $otDutyService
    ) {}

    public function index(Request $request): View
    {
        $query = OtDuty::query();

        if ($request->filled('sister_name')) {
            $query->where('sister_name', $request->string('sister_name'));
        }

        if ($request->filled('technician_name')) {
            $query->where('technician_name', $request->string('technician_name'));
        }

        if ($request->filled('ot_no')) {
            $query->where('ot_no', $request->integer('ot_no'));
        }

        if ($request->filled('shift')) {
            $query->where('shift', $request->string('shift'));
        }

        if ($request->filled('department')) {
            $query->where('department', $request->string('department'));
        }

        if ($request->filled('unit_no')) {
            $query->where('unit_no', $request->integer('unit_no'));
        }

        $otDuties = $query
            ->latest('id')
            ->paginate(15)
            ->withQueryString();

        return view('ot-duty.index', [
            'otDuties' => $otDuties,
            'sisters' => StaffMember::names('sister'),
            'technicians' => StaffMember::names('technician'),
            'otNumbers' => OtDutyOptions::otNumbers(),
            'shifts' => OtDutyOptions::shifts(),
            'departments' => OtDutyOptions::departments(),
            'units' => OtDutyOptions::units(),
        ]);
    }

    public function create(): View
    {
        return view('ot-duty.create', $this->options());
    }

    public function store(OtDutyRequest $request): RedirectResponse
    {
        $this->otDutyService->create($request->validated());

        return redirect()
            ->route('ot-duty.index')
            ->with('success', 'OT duty assignment created successfully.');
    }

    public function show(OtDuty $otDuty): View
    {
        return view('ot-duty.show', compact('otDuty'));
    }

    public function edit(OtDuty $otDuty): View
    {
        return view('ot-duty.edit', array_merge(
            ['otDuty' => $otDuty],
            $this->options()
        ));
    }

    public function update(OtDutyRequest $request, OtDuty $otDuty): RedirectResponse
    {
        $this->otDutyService->update($otDuty, $request->validated());

        return redirect()
            ->route('ot-duty.index')
            ->with('success', 'OT duty assignment updated successfully.');
    }

    public function destroy(OtDuty $otDuty): RedirectResponse
    {
        $this->otDutyService->delete($otDuty);

        return redirect()
            ->route('ot-duty.index')
            ->with('success', 'OT duty assignment deleted successfully.');
    }

    private function options(): array
    {
        return [
            'sisters' => StaffMember::names('sister'),
            'technicians' => StaffMember::names('technician'),
            'otNumbers' => OtDutyOptions::otNumbers(),
            'shifts' => OtDutyOptions::shifts(),
            'departments' => OtDutyOptions::departments(),
            'units' => OtDutyOptions::units(),
            'sections' => OtDutyOptions::sections(),
        ];
    }
}
