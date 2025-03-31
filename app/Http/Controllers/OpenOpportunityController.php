<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;

class OpenOpportunityController extends Controller
{
    public function details($purpose)
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