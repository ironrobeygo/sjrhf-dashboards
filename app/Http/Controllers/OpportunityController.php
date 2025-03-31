<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Opportunity;

class OpportunityController extends Controller
{
    public function type($type)
    {
        $last12Months = getLast12Months();

        // Determine which date column to use based on $type
        switch ($type) {
            case 'target':
                $records = Opportunity::whereNotNull('date_asked')
                    ->whereBetween('date_asked', [$last12Months['cutoff'], $last12Months['now']])
                    ->get();
                $title = 'Target Ask Opportunities';
                break;

            case 'expected':
                $records = Opportunity::whereNotNull('date_expected')
                    ->whereBetween('date_expected', [$last12Months['cutoff'], $last12Months['now']])
                    ->get();
                $title = 'Expected Opportunities';
                break;

            case 'funded':
                $records = Opportunity::whereNotNull('date_closed')
                    ->whereBetween('date_closed', [$last12Months['cutoff'], $last12Months['now']])
                    ->get();
                $title = 'Funded Opportunities';
                break;

            default:
                $records = collect();
                $title = 'Unknown Opportunities';
        }

        return view('opportunities.type.details', compact('records', 'title'));
    }

    public function summary($status)
    {
        // Mapping: cleaned value => full purpose value
        $statusMapping = [
            'funded-closed'     => 'Funded/Closed',
            'identification'    => 'Identification',
            'cultivation'       => 'Cultivation',
            'solicited-ask-made' => 'Solicited - Ask Made',
            'pre-ask'           => 'Pre-Ask',
            'verbal-agreement'  => 'Verbal Agreement',
            'qualification'     => 'Qualification',
            'no-response'       => 'No Response',
            'declined'          => 'Declined',
            'deferred'          => 'Deferred',
            'never-submitted'   => 'Never Submitted'
        ];

        $status = $statusMapping[$status] ?? null;
        if (!$status) {
            abort(404, 'Status not found.');
        }

        // Only active proposals (not date-limited) with the clicked proposal_status
        $records = Opportunity::active()
            ->where('proposal_status', $status)
            ->get();

        $title = "Active Proposals with Status: $status";

        return view('opportunities.summary', compact('records', 'title'));
    }

    public function typeDetails($type)
    {
        $last12Months = getLast12Months();

        if ($type === 'solicited-ask-made') {
            $opportunities = Opportunity::proposalStatus('Solicited - Ask Made', $last12Months['cutoff'], $last12Months['now'])->get();
            $title = 'Solicited - Ask Made Opportunities (Last 12 Months)';
        } elseif ($type === 'funded-closed') {
            $opportunities = Opportunity::fundedClosed($last12Months['cutoff'], $last12Months['now'])->get();
            $title = 'Funded/Closed Opportunities (Last 12 Months)';
        } else {
            abort(404, 'Opportunity type not found.');
        }
        
        return view('opportunities.details', compact('opportunities', 'title'));
    }

    public function fundedOpportunity(){
        $fiscalDates = getFiscalYearDates(Carbon::now());

        // Retrieve all funded opportunities within the current fiscal year
        $fundedOpportunities = Opportunity::where('proposal_status', 'Funded/Closed')
            ->whereNotNull('date_closed')
            ->whereBetween('date_closed', [$fiscalDates['start'], $fiscalDates['end']])
            ->get();

        // Pass the data to the view for display
        return view('opportunities.funded.details', [
            'fundedOpportunities' => $fundedOpportunities,
            'fiscalStart'         => $fiscalDates['start'],
            'fiscalEnd'           => $fiscalDates['end'],
        ]);
    }

    public function purposeDetails($purpose)
    {
        // Mapping: cleaned value => full purpose value
        $purposeMapping = [
            'sponsorship'       => 'Sponsorship',
            'major'             => 'Major ($10k-$99k)',
            'planned'           => 'Planned',
            'transformational'  => 'Transformational ($1M+)',
            'leadership'        => 'Leadership ($100k-$999k)',  // Adjust as stored
            'mid-level'         => 'Mid-Level ($1k-$9,999)'
        ];

        $purpose = $purposeMapping[$purpose] ?? null;
        if (!$purpose) {
            abort(404, 'Purpose not found.');
        }

        // Retrieve all active opportunities with the given purpose
        $opportunities = Opportunity::openProposals()
            ->where('purpose', $purpose )
            ->get();

        return view('opportunities.open.purpose.details', compact('opportunities', 'purpose'));
    }
}
