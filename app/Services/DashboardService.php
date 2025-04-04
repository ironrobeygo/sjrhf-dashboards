<?php

namespace App\Services;

use App\Services\Dashboard\FunnelService;
use App\Services\Dashboard\ProposalService;
use App\Services\Dashboard\FundraiserService;
use Carbon\Carbon;

class DashboardService
{
    protected $funnelService;
    protected $proposalService;
    protected $fundraiserService;

    public function __construct(
        FunnelService $funnelService,
        ProposalService $proposalService,
        FundraiserService $fundraiserService
    ) {
        $this->funnelService = $funnelService;
        $this->proposalService = $proposalService;
        $this->fundraiserService = $fundraiserService;
    }

    public function getDashboardData()
    {
        $now = Carbon::now();
        $cutoff = $now->copy()->subMonths(12);
        $fiscalDates = $this->getFiscalYearDates($now);

        $funnel = $this->funnelService->getFunnelData($cutoff, $now);
        $proposalSummary = $this->proposalService->getProposalSummary();
        $openProposalsCounts = $this->proposalService->getOpenProposalsCounts();
        $fundedData = $this->proposalService->getFundedData($fiscalDates);
        $solicitedAskCount = $this->proposalService->getSolicitedAskCount($cutoff, $now);
        $fundedClosedCount = $this->proposalService->getFundedClosedCount($cutoff, $now);
        $fundraiserCategoryData = $this->fundraiserService->getFundraiserCategoryData();
        $fundraiserTypeData = $this->fundraiserService->getFundraiserTypeData();

        return [
            'funnel'                        => $funnel,
            'summaryLabels'                 => $proposalSummary['labels'],
            'summaryCounts'                 => $proposalSummary['counts'],
            'individualOpenCount'           => $openProposalsCounts['individual'],
            'organizationOpenCount'         => $openProposalsCounts['organization'],
            'fundedData'                    => $fundedData,
            'purposeLabels'                 => $openProposalsCounts['purpose']['labels'],
            'purposeCounts'                 => $openProposalsCounts['purpose']['counts'],
            'solicitedAskCount'             => $solicitedAskCount,
            'fundedClosedCount'             => $fundedClosedCount,
            'fundraiserCategoryLabels'      => $fundraiserCategoryData['labels'],
            'fundraiserActionCategoryChart' => $fundraiserCategoryData['chart'],
            'fundraiserTypeLabels'          => $fundraiserTypeData['labels'],
            'fundraiserActionTypeChart'     => $fundraiserTypeData['chart'],
        ];
    }

    private function getFiscalYearDates(Carbon $now)
    {
        if ($now->month >= 4) {
            return [
                'start' => Carbon::create($now->year, 4, 1, 0, 0, 0),
                'end'   => Carbon::create($now->year + 1, 3, 31, 23, 59, 59),
            ];
        }
        return [
            'start' => Carbon::create($now->year - 1, 4, 1, 0, 0, 0),
            'end'   => Carbon::create($now->year, 3, 31, 23, 59, 59),
        ];
    }
}
