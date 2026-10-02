<?php

namespace App\Http\Controllers;

use App\Models\OtDuty;
use App\Models\StaffMember;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class StaffDirectoryController extends Controller
{
    public function index(): View
    {
        $members = StaffMember::query()->orderBy('name')->get()->groupBy('type');

        return view('staff-directory.index', compact('members'));
    }

    public function create(Request $request): View
    {
        $type = $request->query('type');

        return view('staff-directory.form', [
            'staffMember' => null,
            'type' => in_array($type, StaffMember::TYPES, true) ? $type : 'sister',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'type' => ['required', Rule::in(StaffMember::TYPES)],
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('staff_members', 'name')->where('type', $request->input('type')),
            ],
            'mobile' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s().-]{7,30}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        StaffMember::create($data);

        return redirect()->route('staff-directory.index')->with('success', 'Staff member added.');
    }

    public function edit(StaffMember $staffMember): View
    {
        return view('staff-directory.form', [
            'staffMember' => $staffMember,
            'type' => $staffMember->type,
        ]);
    }

    public function update(Request $request, StaffMember $staffMember): RedirectResponse
    {
        $data = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('staff_members', 'name')
                    ->where('type', $staffMember->type)
                    ->ignore($staffMember->id),
            ],
            'mobile' => ['nullable', 'string', 'max:30', 'regex:/^\+?[0-9\s().-]{7,30}$/'],
            'email' => ['nullable', 'email', 'max:255'],
            'address' => ['nullable', 'string', 'max:500'],
        ]);

        DB::transaction(function () use ($staffMember, $data) {
            $oldName = $staffMember->name;
            $staffMember->update($data);

            if ($oldName !== $staffMember->name) {
                $column = $staffMember->type === 'sister' ? 'sister_name' : 'technician_name';
                OtDuty::query()->where($column, $oldName)->update([$column => $staffMember->name]);
            }
        });

        return redirect()->route('staff-directory.index')->with('success', 'Staff details updated.');
    }

    public function destroy(StaffMember $staffMember): RedirectResponse
    {
        $column = $staffMember->type === 'sister' ? 'sister_name' : 'technician_name';

        if (OtDuty::query()->where($column, $staffMember->name)->exists()) {
            return back()->withErrors([
                'staff_member' => 'This staff member has OT assignments and cannot be removed.',
            ]);
        }

        $staffMember->delete();

        return redirect()->route('staff-directory.index')->with('success', 'Staff member removed.');
    }
}