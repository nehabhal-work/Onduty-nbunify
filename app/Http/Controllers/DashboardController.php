<?php

namespace App\Http\Controllers;

use App\Models\OtDuty;
use Illuminate\Routing\Controller;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        $today = today();

        return view('dashboard', [
            'totalAssignments' => OtDuty::count(),
            'todayAssignments' => OtDuty::whereDate('date_time', $today)->count(),
            'upcomingCount' => OtDuty::where('date_time', '>=', now())->count(),
            'upcomingAssignments' => OtDuty::where('date_time', '>=', now())
                ->orderBy('date_time')
                ->limit(6)
                ->get(),
            'sectionCounts' => OtDuty::query()
                ->selectRaw('section, count(*) as total')
                ->groupBy('section')
                ->orderByDesc('total')
                ->get(),
        ]);
    }
}