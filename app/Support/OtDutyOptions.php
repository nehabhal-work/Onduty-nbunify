<?php

namespace App\Support;

class OtDutyOptions
{
    public static function sections(): array
    {
        return [
            '2nd floor section',
            '4th floor section',
            'LR section',
            'Recovery section',
            'Scope OT section',
        ];
    }

    public static function sisters(): array
    {
        $sisters = [
            'Mrunali',
            'Pallavi',
            'Sara',
            'Prathamesh',
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
            'Anuja',
            'Avinash',
            'Surjeet',
            'Prasad',
            'Sudha',
            'Rahel',
            'Kailash',
            'Aarya',
        ];

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
            'Afternoon',
            'Evening',
            'Night',
            'Double Duty',
        ];
    }

    public static function departments(): array
    {
        return [
            'OBGY',
            'ENT',
            'Ortho',
            'Neuro / Spine',
            'Euro',
            'Followup OT',
            'Surgery',
            'Obthormology',
            'D-Wing',
        ];
    }

    public static function units(): array
    {
        return [1, 2, 3, 4, 5, 6];
    }
}
