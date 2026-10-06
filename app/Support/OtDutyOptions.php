<?php

namespace App\Support;

class OtDutyOptions
{
    public static function sections(): array
    {
        return [
            '2nd floor',
            '4th floor',
            'LR OT',
            'Recovery',
            'Scope OT',
            'Night On Call',
        ];
    }

    public static function sisters(): array
    {
        $sisters = array_unique([
            '-',
            'Mrunal',
            'Pallavi',
            'Dipali N',
            'Sai',
            'Ashfaq',
            'Tushar',
            'Sagar',
            'Sahil',
            'Mayuresh',
            'Abdul',
            'Noor',
            'Dilip',
            'Surekha',
            'Usha',
            'Mery',
            'Shraddha',
            'Sonali T',
            'Aurang',
            'Nirmala',
            'Avinash',
            'Surjeet',
            'Prasad',
            'Sudha',
            'Rahel',
            'Kailash',
            'Aarya',
            'Sachin',
            'Shubham',
            'Purva',
            'Jai',
            'Ruchita',
            'Rupali',
            'Kirti',
            'Roshani',
            'Madhukar',
            'Renuka',
            'Amrapali',
        ]);

        sort($sisters, SORT_STRING | SORT_FLAG_CASE);

        return $sisters;
    }

    public static function technicians(): array
    {
        return self::sisters();
    }

    public static function otNumbers(): array
    {
        return [
            0,
            1,
            2,
            3,
            4,
            5,
            6,
            7,
            8,
            9,
            10,
            11,
            17,
            18,
            19,
            20,
            21,
        ];
    }

    public static function shifts(): array
    {
        return [
            'Morning',
            'Evening',
            'Night',
            'Double Duty',
        ];
    }

    public static function departments(): array
    {
        $departments = [
            'OBGY',
            'ENT',
            'Ortho',
            'Neuro / Spine',
            'Euro',
            'Followup OT',
            'Surgery',
            'Ophthalmic',
            'D-Wing',
            'Oncology',
            'Pseudodental',
            '-',
        ];

        sort($departments, SORT_STRING | SORT_FLAG_CASE);

        return $departments;
    }

    public static function units(): array
    {
        return [0, 1, 2, 3, 4, 5, 6];
    }
}
