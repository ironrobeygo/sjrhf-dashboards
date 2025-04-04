<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ProposalService;

class ProposalController extends Controller
{
    protected $proposalService;

    public function __construct(ProposalService $proposalService)
    {
        $this->proposalService = $proposalService;
    }

    public function details($group)
    {
        $data = $this->proposalService->getProposalsByGroup($group);
        return view('proposal.open.details', $data);
    }

    public function summary($status)
    {
        $data = $this->proposalService->getProposalSummary($status);
        return view('proposal.summary', $data);
    }
}