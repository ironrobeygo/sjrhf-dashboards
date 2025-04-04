<?php

namespace App\Services\Dashboard;

use App\Models\Opportunity;
use Carbon\Carbon;

class ProposalService
{
    public function getProposalSummary()
    {
        $groupedStatus = Opportunity::getActiveProposals();
        return [
            'labels' => $groupedStatus->keys()->toArray(),
            'counts' => $groupedStatus->map->count()->values()->toArray()
        ];
    }

    public function getOpenProposalsCounts()
    {
        $openProposals = Opportunity::openProposals()->get();
        $counts = [
            'individual'   => $openProposals->where('key_indicator', 'Individual')->count(),
            'organization' => $openProposals->where('key_indicator', 'Organization')->count(),
            'purpose'      => $this->getProposalPurposeCounts($openProposals)
        ];
        return $counts;
    }

    private function getProposalPurposeCounts($openProposals)
    {
        $groupedPurposes = $openProposals->groupBy('purpose');
        return [
            'labels' => $groupedPurposes->keys()->toArray(),
            'counts' => $groupedPurposes->map->count()->values()->toArray()
        ];
    }

    public function getFundedData(array $fiscalDates)
    {
        return Opportunity::where('proposal_status', 'Funded/Closed')
            ->whereNotNull('date_closed')
            ->whereBetween('date_closed', [$fiscalDates['start'], $fiscalDates['end']])
            ->selectRaw('count(*) as total_funded_opportunities, sum(amount_funded) as sum_funded')
            ->first();
    }

    public function getSolicitedAskCount(Carbon $cutoff, Carbon $now)
    {
        return Opportunity::proposalStatus('Solicited - Ask Made', $cutoff, $now)->count();
    }

    public function getFundedClosedCount(Carbon $cutoff, Carbon $now)
    {
        return Opportunity::fundedClosed($cutoff, $now)->count();
    }
}