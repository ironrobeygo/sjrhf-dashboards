<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;
use Carbon\Carbon;
use App\Models\Action;
use Illuminate\Support\Facades\DB;


class DashboardController extends Controller
{
    public function index(){
        $cutoff = Carbon::now()->subMonths(12);
        $now = Carbon::now();

        // Funnel Chart (12-month)
        $funnel = [
            'Target Ask' => Opportunity::active()->whereBetween('date_asked', [$cutoff, $now])->sum('target_ask'),
            'Expected'   => Opportunity::active()->whereBetween('date_expected', [$cutoff, $now])->sum('amount_expected'),
            'Funded'     => Opportunity::active()->whereBetween('date_closed', [$cutoff, $now])->sum('amount_funded'),
        ];

        // Prospect Proposal Summary Chart (Active Only)
        $groupedStatus = Opportunity::getActiveProposals();
        $summaryLabels = $groupedStatus->keys()->toArray();
        $summaryCounts = $groupedStatus->map->count()->values()->toArray();

        $openProposals = Opportunity::openProposals()->get();
        $individualOpenCount = $openProposals->where('key_indicator', 'Individual')->count();
        $organizationOpenCount = $openProposals->where('key_indicator', 'Organization')->count();

        $fiscalDates = getFiscalYearDates(Carbon::now());

        $fundedData = Opportunity::where('proposal_status', 'Funded/Closed')
            ->whereNotNull('date_closed')
            ->whereBetween('date_closed', [$fiscalDates['start'], $fiscalDates['end']])
            ->selectRaw('count(*) as total_funded_opportunities, sum(amount_funded) as sum_funded')
            ->first();
        
        // Group by purpose and count how many proposals in each
        $groupedPurposes = $openProposals->groupBy('purpose');
        $purposeLabels = $groupedPurposes->keys()->toArray();
        $purposeCounts = $groupedPurposes->map->count()->values()->toArray();

        $solicitedAskCount = Opportunity::proposalStatus('Solicited - Ask Made', $cutoff, $now)->count();
        $fundedClosedCount = Opportunity::fundedClosed($cutoff, $now)->count();

        $actionCategories = Action::select([
                DB::raw("TRIM(action_solicitor_list) as fundraiser"),
                'action_category',
                DB::raw('COUNT(*) as total')
            ])
            ->whereBetween('action_completed_on', [$fiscalDates['start'], $fiscalDates['end']])
            ->whereNotNull('action_solicitor_list')
            ->whereNotNull('action_category')
            ->groupBy('fundraiser', 'action_category')
            ->get()
            ->groupBy('fundraiser');

        $fundraisersByCategory = $actionCategories->keys();
        $categories = ['Email', 'Mailing', 'Meeting', 'Phone Call'];
        
        $fundraiserActionsByCategoryChart = [];
        foreach ($categories as $category) {
            $fundraiserActionsByCategoryChart[] = [
                'label' => $category,
                'data' => $fundraisersByCategory->map(fn($f) => $actionCategories[$f]->firstWhere('action_category', $category)?->total ?? 0)->toArray(),
                'backgroundColor' => match ($category) {
                    'Email' => '#42A5F5',
                    'Mailing' => '#FFCA28',
                    'Meeting' => '#66BB6A',
                    'Phone Call' => '#AB47BC',
                    default => '#ccc'
                }
            ];
        }

        $actionTypes = Action::select([
            DB::raw("TRIM(action_solicitor_list) as fundraiser"),
            'action_type',
            DB::raw('COUNT(*) as total')
        ])
        ->whereBetween('action_completed_on', [$fiscalDates['start'], $fiscalDates['end']])
        ->whereNotNull('action_solicitor_list')
        ->whereNotNull('action_type')
        ->groupBy('fundraiser', 'action_type')
        ->get()
        ->groupBy('fundraiser');

        $fundraisersByType = $actionTypes->keys();
<<<<<<< HEAD
        $types = ['Card', 
            'Cultivation',
            'Follow Up',
            'Identification',
            'Information Requested',
            'Left a Voicemail',
=======
        $types = [
            'Cultivation',
>>>>>>> c7ba2e78cc58f6ccdc01fe27a367150ddacaa009
            'Meaningful Move',
            'Other',
            'Qualification',
            'Report Back',
            'Solicitation',
            'Stewardship',
<<<<<<< HEAD
            'Tour'
=======
>>>>>>> c7ba2e78cc58f6ccdc01fe27a367150ddacaa009
        ];
        
        $fundraiserActionsByTypeChart = [];
        foreach ($types as $type) {
            $fundraiserActionsByTypeChart[] = [
                'label' => $type,
                'data' => $fundraisersByType->map(fn($f) => $actionTypes[$f]->firstWhere('action_type', $type)?->total ?? 0)->toArray(),
                'backgroundColor' => match ($type) {
<<<<<<< HEAD
                    'Card' => '#F06292',
                    'Cultivation' => '#BA68C8',
                    'Follow Up' => '#64B5F6',
                    'Identification' => '#4DB6AC',
                    'Information Requested' => '#FFD54F',
                    'Left a Voicemail' => '#90A4AE',
=======
                    'Cultivation' => '#BA68C8',
>>>>>>> c7ba2e78cc58f6ccdc01fe27a367150ddacaa009
                    'Meaningful Move' => '#81C784',
                    'Other' => '#E0E0E0',
                    'Qualification' => '#7986CB',
                    'Report Back' => '#AED581',
                    'Solicitation' => '#FF8A65',
                    'Stewardship' => '#66BB6A',
<<<<<<< HEAD
                    'Tour' => '#4DD0E1',
=======
>>>>>>> c7ba2e78cc58f6ccdc01fe27a367150ddacaa009
                    default => '#ccc'
                }
            ];
        }
            
        return view('dashboard', [
            'funnel'                => $funnel,
            'summaryLabels'         => $summaryLabels,
            'summaryCounts'         => $summaryCounts,
            'individualOpenCount'   => $individualOpenCount,
            'organizationOpenCount' => $organizationOpenCount,
            'fundedData'            => $fundedData,
            'purposeLabels'         => $purposeLabels,
            'purposeCounts'         => $purposeCounts,
            'solicitedAskCount'     => $solicitedAskCount,
            'fundedClosedCount'     => $fundedClosedCount,
            'fundraiserCategoryLabels'      => $fundraisersByCategory,
            'fundraiserActionCategoryChart' => $fundraiserActionsByCategoryChart,
            'fundraiserTypeLabels'      => $fundraisersByType,
            'fundraiserActionTypeChart' => $fundraiserActionsByTypeChart
        ]);
    }

    private function getFiscalYearDates(\Carbon\Carbon $now)
    {
        if ($now->month >= 4) {
            return [
                'start' => \Carbon\Carbon::create($now->year, 4, 1, 0, 0, 0),
                'end'   => \Carbon\Carbon::create($now->year + 1, 3, 31, 23, 59, 59),
            ];
        }

        return [
            'start' => \Carbon\Carbon::create($now->year - 1, 4, 1, 0, 0, 0),
            'end'   => \Carbon\Carbon::create($now->year, 3, 31, 23, 59, 59),
        ];
    }
}
