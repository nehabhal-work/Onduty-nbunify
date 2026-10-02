<?php

namespace App\Http\Requests;

use App\Support\OtDutyOptions;
use App\Models\StaffMember;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class OtDutyRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $section = $this->input('section');
        $requiresTechnician = $section !== 'Recovery section';
        $requiresOtNumber = in_array($section, ['2nd floor section', '4th floor section'], true);
        $requiresDepartment = $section !== 'Recovery section';
        $requiresUnit = in_array($section, ['2nd floor section', 'LR section', 'Scope OT section'], true);
        $allowedOtNumbers = match ($section) {
            '2nd floor section' => array_values(array_diff(OtDutyOptions::otNumbers(), [17, 18, 19, 20, 21])),
            '4th floor section' => [17, 18, 19, 20, 21],
            default => OtDutyOptions::otNumbers(),
        };

        return [
            'section' => [
                'required',
                'string',
                Rule::in(OtDutyOptions::sections()),
            ],
            'sister_name' => [
                'required',
                'string',
                'max:100',
                Rule::exists('staff_members', 'name')->where('type', 'sister'),
            ],
            'technician_name' => [
                Rule::requiredIf($requiresTechnician),
                'nullable',
                'string',
                'max:100',
                Rule::exists('staff_members', 'name')->where('type', 'technician'),
            ],
            'date_time' => [
                'required',
                'date_format:Y-m-d\TH:i',
            ],
            'ot_no' => [
                Rule::requiredIf($requiresOtNumber),
                'nullable',
                'integer',
                Rule::in($allowedOtNumbers),
            ],
            'shift' => [
                'required',
                'string',
                Rule::in(OtDutyOptions::shifts()),
            ],
            'department' => [
                Rule::requiredIf($requiresDepartment),
                'nullable',
                'string',
                Rule::in($section === 'LR section' ? ['OBGY'] : OtDutyOptions::departments()),
            ],
            'unit_no' => [
                Rule::requiredIf($requiresUnit),
                'nullable',
                'integer',
                Rule::in(OtDutyOptions::units()),
            ],
            'surgery' => [
                'nullable',
                'string',
                'max:255',
            ],
            'remarks' => [
                'nullable',
                'string',
                'max:2000',
            ],
        ];
    }

    protected function prepareForValidation(): void
    {
        $section = $this->input('section');
        $values = [];

        if ($section === 'LR section') {
            $values['department'] = 'OBGY';
        } elseif ($section === 'Recovery section') {
            $values['department'] = null;
            $values['technician_name'] = null;
            $values['surgery'] = null;
        }

        if (in_array($section, ['LR section', 'Recovery section', 'Scope OT section'], true)) {
            $values['ot_no'] = null;
        }

        if (in_array($section, ['4th floor section', 'Recovery section'], true)) {
            $values['unit_no'] = null;
        }

        $this->merge($values);
    }

    public function attributes(): array
    {
        return [
            'sister_name' => 'sister name',
            'technician_name' => 'technician name',
            'date_time' => 'date and time',
            'section' => 'section',
            'ot_no' => 'OT number',
            'unit_no' => 'unit number',
        ];
    }
}
