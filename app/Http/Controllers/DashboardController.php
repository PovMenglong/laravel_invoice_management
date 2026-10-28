<?php

namespace App\Http\Controllers;

use App\Models\Invoice;

class DashboardController extends Controller
{
    public function __invoke()
    {
        // One query: count and sum for every status at once
        $byStatus = Invoice::selectRaw('status, count(*) as n, sum(total) as amount')
            ->groupBy('status')
            ->get()
            ->keyBy('status');

        $overdue = Invoice::overdue()
            ->selectRaw('count(*) as n, coalesce(sum(total), 0) as amount')
            ->first();

        $paidThisMonth = Invoice::where('status', 'paid')
            ->whereBetween('paid_at', [now()->startOfMonth(), now()->endOfMonth()])
            ->sum('total');

        return view('dashboard', [
            'outstanding'   => $byStatus['issued']->amount ?? 0,
            'outstandingN'  => $byStatus['issued']->n ?? 0,
            'drafts'        => $byStatus['draft']->n ?? 0,
            'overdue'       => $overdue,
            'paidThisMonth' => $paidThisMonth,
            'recent'        => Invoice::with('customer:id,name')->latest('id')->limit(5)->get(),
        ]);
    }
}
