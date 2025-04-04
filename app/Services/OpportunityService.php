<?php

namespace App\Services;

use Carbon\Carbon;
use App\Models\Opportunity;

class OpportunityService
{
    protected $statusMapping = [
        'funded-closed'      => 'Funded/Closed',
        'identification'     => 'Identification',
        'cultivation'        => 'Cultivation',
        'solicited-ask-made' => 'Solicited - Ask Made',
        'pre-ask'            => 'Pre-Ask',
        'verbal-agreement'   => 'Verbal Agreement',
        'qualification'      => 'Qualification',
        'no-response'        => 'No Response',
        'declined'           => 'Declined',
        'deferred'           => 'Deferred',
        'never-submitted'    => 'Never Submitted'
    ];

    protected $purposeMapping = [
        'sponsorship'      => 'Sponsorship',
        'major'            => 'Major ($10k-$99k)',
        'planned'          => 'Planned',
        'transformational' => 'Transformational ($1M+)',
        'leadership'       => 'Leadership ($100k-$999k)',
        'mid-level'        => 'Mid-Level ($1k-$9,999)'
    ];

    /**
     * Returns an array with cutoff and current dates for the last 12 months.
     */
    public function getLast12MonthsDates(): array
    {
        return [
            'cutoff' => Carbon::now()->subMonths(12),
            'now'    => Carbon::now()
        ];
    }

    /**
     * Returns the fiscal start and end dates based on the current date.
     */
    public function getFiscalYearDates(Carbon $now): array
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

    /**
     * Get opportunities by type (target, expected, funded) for the last 12 months.
     */
    public function getOpportunitiesByType(string $type, int $perPage = 15): array
    {
        $dates = $this->getLast12MonthsDates();

        switch ($type) {
            case 'target':
                $records = Opportunity::whereNotNull('date_asked')
                    ->whereBetween('date_asked', [$dates['cutoff'], $dates['now']])
                    ->paginate($perPage);
                $title = 'Target Ask Opportunities';
                break;

            case 'expected':
                $records = Opportunity::whereNotNull('date_expected')
                    ->whereBetween('date_expected', [$dates['cutoff'], $dates['now']])
                    ->paginate($perPage);
                $title = 'Expected Opportunities';
                break;

            case 'funded':
                $records = Opportunity::whereNotNull('date_closed')
                    ->whereBetween('date_closed', [$dates['cutoff'], $dates['now']])
                    ->paginate($perPage);
                $title = 'Funded Opportunities';
                break;

            default:
                $records = Opportunity::paginate(0);
                $title = 'Unknown Opportunities';
        }

        return compact('records', 'title');
    }

    /**
     * Get a summary of opportunities filtered by proposal status.
     */
    public function getSummaryByStatus(string $status, int $perPage = 15): array
    {
        $statusFull = $this->statusMapping[$status] ?? null;
        if (!$statusFull) {
            abort(404, 'Status not found.');
        }

        $records = Opportunity::active()
            ->where('proposal_status', $statusFull)
            ->paginate($perPage);
        $title = "Active Proposals with Status: $statusFull";

        return compact('records', 'title');
    }

    /**
     * Get details for opportunity types that require special query logic.
     */
    public function getTypeDetails(string $type): array
    {
        $dates = $this->getLast12MonthsDates();

        if ($type === 'solicited-ask-made') {
            $opportunities = Opportunity::proposalStatus('Solicited - Ask Made', $dates['cutoff'], $dates['now'])->get();
            $title = 'Solicited - Ask Made Opportunities (Last 12 Months)';
        } elseif ($type === 'funded-closed') {
            $opportunities = Opportunity::fundedClosed($dates['cutoff'], $dates['now'])->get();
            $title = 'Funded/Closed Opportunities (Last 12 Months)';
        } else {
            abort(404, 'Opportunity type not found.');
        }

        return compact('opportunities', 'title');
    }

    /**
     * Get all funded opportunities within the current fiscal year.
     */
    public function getFundedOpportunities(int $perPage = 15): array
    {
        $fiscalDates = $this->getFiscalYearDates(Carbon::now());
        $fundedOpportunities = Opportunity::where('proposal_status', 'Funded/Closed')
            ->whereNotNull('date_closed')
            ->whereBetween('date_closed', [$fiscalDates['start'], $fiscalDates['end']])
            ->paginate($perPage);

        return [
            'fundedOpportunities' => $fundedOpportunities,
            'fiscalDates'         => $fiscalDates
        ];
    }

    /**
     * Get active opportunities by purpose.
     */
    public function getOpportunitiesByPurpose(string $purpose, int $perPage = 15): array
    {
        $purposeFull = $this->purposeMapping[$purpose] ?? null;
        if (!$purposeFull) {
            abort(404, 'Purpose not found.');
        }

        $opportunities = Opportunity::openProposals()
            ->where('purpose', $purposeFull)
            ->paginate($perPage);

        return compact('opportunities', 'purpose');
    }
}