<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Opportunity;

class OpportunityController extends Controller
{
    public function show($type)
    {
        $cutoff = Carbon::now()->subMonths(12);
        $now = Carbon::now();

        // Determine which date column to use based on $type
        switch ($type) {
            case 'target':
                $records = Opportunity::whereNotNull('date_asked')
                    ->whereBetween('date_asked', [$cutoff, $now])
                    ->get();
                $title = 'Target Ask Opportunities';
                break;

            case 'expected':
                $records = Opportunity::whereNotNull('date_expected')
                    ->whereBetween('date_expected', [$cutoff, $now])
                    ->get();
                $title = 'Expected Opportunities';
                break;

            case 'funded':
                $records = Opportunity::whereNotNull('date_closed')
                    ->whereBetween('date_closed', [$cutoff, $now])
                    ->get();
                $title = 'Funded Opportunities';
                break;

            default:
                $records = collect();
                $title = 'Unknown Opportunities';
        }

        return view('opportunities.details', compact('records', 'title'));
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

    public function details($type)
    {
        $cutoff = Carbon::now()->subMonths(12);
        $now = Carbon::now();

        if ($type === 'solicited-ask-made') {
            $opportunities = Opportunity::proposalStatus('Solicited - Ask Made', $cutoff, $now)->get();
            $title = 'Solicited - Ask Made Opportunities (Last 12 Months)';
        } elseif ($type === 'funded-closed') {
            $opportunities = Opportunity::fundedClosed($cutoff, $now)->get();
            $title = 'Funded/Closed Opportunities (Last 12 Months)';
        } else {
            abort(404, 'Opportunity type not found.');
        }
        
        return view('opportunities.opportunity-details', compact('opportunities', 'title'));
    }
}
