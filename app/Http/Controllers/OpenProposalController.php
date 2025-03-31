<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;

class OpenProposalController extends Controller
{
    public function details($group)
    {
        // Filter opportunities based on the group clicked and open proposal criteria.
        $proposals = Opportunity::openProposals()
            ->where('key_indicator', $group) // 'I' for individuals, 'O' for organizations
            ->get();

        return view('proposal.open.details', compact('proposals', 'group'));
    }
}