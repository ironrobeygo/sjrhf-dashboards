<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Carbon\Carbon;
use App\Models\Opportunity;

class FundedOpportunityController extends Controller
{
    public function index()
    {
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
}
