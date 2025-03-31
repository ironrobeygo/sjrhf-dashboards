<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;
use Carbon\Carbon;


class DashboardController extends Controller
{
    public function index(){
        $cutoff = Carbon::now()->subMonths(12);
        $now = Carbon::now();

        // Funnel Chart (12-month)
        $funnel = [
            'Target Ask' => Opportunity::active()->whereBetween('date_asked', [$cutoff, $now])->sum('target_ask'),
            'Expected'   => Opportunity::active()->whereBetween('date_expected', [$cutoff, $now])->sum('amount_expected'),
            'Funded'     => Opportunity::active()->whereBetween('date_closed', [$cutoff, $now])->sum('amount_funded'),
        ];

        // Prospect Proposal Summary Chart (Active Only)
        $groupedStatus = Opportunity::getActiveProposals();
        $summaryLabels = $groupedStatus->keys()->toArray();
        $summaryCounts = $groupedStatus->map->count()->values()->toArray();

        $openProposals = Opportunity::openProposals()->get();
        $individualOpenCount = $openProposals->where('key_indicator', 'Individual')->count();
        $organizationOpenCount = $openProposals->where('key_indicator', 'Organization')->count();

        $fiscalDates = getFiscalYearDates(Carbon::now());

        $fundedData = Opportunity::where('proposal_status', 'Funded/Closed')
            ->whereNotNull('date_closed')
            ->whereBetween('date_closed', [$fiscalDates['start'], $fiscalDates['end']])
            ->selectRaw('count(*) as total_funded_opportunities, sum(amount_funded) as sum_funded')
            ->first();
        
        // Group by purpose and count how many proposals in each
        $groupedPurposes = $openProposals->groupBy('purpose');
        $purposeLabels = $groupedPurposes->keys()->toArray();
        $purposeCounts = $groupedPurposes->map->count()->values()->toArray();

        $solicitedAskCount = Opportunity::proposalStatus('Solicited - Ask Made', $cutoff, $now)->count();
        $fundedClosedCount = Opportunity::fundedClosed($cutoff, $now)->count();

        return view('dashboard', [
            'funnel'                => $funnel,
            'summaryLabels'         => $summaryLabels,
            'summaryCounts'         => $summaryCounts,
            'individualOpenCount'   => $individualOpenCount,
            'organizationOpenCount' => $organizationOpenCount,
            'fundedData'            => $fundedData,
            'purposeLabels'         => $purposeLabels,
            'purposeCounts'         => $purposeCounts,
            'solicitedAskCount'     => $solicitedAskCount,
            'fundedClosedCount'     => $fundedClosedCount,
        ]);
    }

    private function getFiscalYearDates(\Carbon\Carbon $now)
    {
        if ($now->month >= 4) {
            return [
                'start' => \Carbon\Carbon::create($now->year, 4, 1, 0, 0, 0),
                'end'   => \Carbon\Carbon::create($now->year + 1, 3, 31, 23, 59, 59),
            ];
        }

        return [
            'start' => \Carbon\Carbon::create($now->year - 1, 4, 1, 0, 0, 0),
            'end'   => \Carbon\Carbon::create($now->year, 3, 31, 23, 59, 59),
        ];
    }
}
