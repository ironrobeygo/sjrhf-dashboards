<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;

class ProposalController extends Controller
{
    public function details($group)
    {
        // Filter opportunities based on the group clicked and open proposal criteria.
        $proposals = Opportunity::openProposals()
            ->where('key_indicator', $group) // 'I' for individuals, 'O' for organizations
            ->get();

        return view('proposal.open.details', compact('proposals', 'group'));
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
}
