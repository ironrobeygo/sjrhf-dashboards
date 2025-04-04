<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\OpportunityService;
use Carbon\Carbon;

class OpportunityController extends Controller
{
    protected $opportunityService;

    public function __construct(OpportunityService $opportunityService)
    {
        $this->opportunityService = $opportunityService;
    }

    public function type($type)
    {
        $data = $this->opportunityService->getOpportunitiesByType($type);
        return view('opportunities.type.details', $data);
    }

    public function summary($status)
    {
        $data = $this->opportunityService->getSummaryByStatus($status);
        return view('opportunities.summary', $data);
    }

    public function typeDetails($type)
    {
        $data = $this->opportunityService->getTypeDetails($type);
        return view('opportunities.details', $data);
    }

    public function fundedOpportunity()
    {
        $data = $this->opportunityService->getFundedOpportunities();
        return view('opportunities.funded.details', [
            'fundedOpportunities' => $data['fundedOpportunities'],
            'fiscalStart'         => $data['fiscalDates']['start'],
            'fiscalEnd'           => $data['fiscalDates']['end'],
        ]);
    }

    public function purposeDetails($purpose)
    {
        $data = $this->opportunityService->getOpportunitiesByPurpose($purpose);
        return view('opportunities.open.purpose.details', $data);
    }
}