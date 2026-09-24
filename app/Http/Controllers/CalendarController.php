<?php

namespace App\Http\Controllers;

use App\Models\Calendar;
use Carbon\Carbon;

class CalendarController extends Controller
{
    public function index()
    {
        return view('calendars.index');
    }

    public function showDashboard()
    {
        /* データが入っていない月も0円で表示できるように、過去6か月分の「Y-m => 0」の
            初期配列を作る（例：2026-04～2026-09）
        */
        $months = collect();
        for ($i = 5; $i >= 0; $i--) {
            $monthKey = Carbon::now()->subMonth($i)->format('Y-m');
            $months->put($monthKey, 0);
        }

        $dbTotals = Calendar::with('chores')
            ->where('chore_day', '>=', Carbon::now()->subMonths(6)->startOfMonth())
            ->get()
            ->groupBy(function ($item) {
                return Carbon::parse($item->chore_day)->format('Y-m'); // 月単位でグループ化
            })
            ->map(function ($dayCalendars) {
                // 月ごとの合計額を計算
                return $dayCalendars->sum(function ($calendar) {
                    return $calendar->chores->sum('price');
                });
            })
            ->sortKeys();

        $monthlyTotals = $months->merge($dbTotals)->sortKeys();

        $maxAmount = $monthlyTotals->max() ?: 100;

        return view('calendars.dashboard', compact('monthlyTotals', 'maxAmount'));
    }
}
