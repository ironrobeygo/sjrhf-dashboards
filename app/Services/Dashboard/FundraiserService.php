<?php

namespace App\Services\Dashboard;

use App\Models\Action;
use Illuminate\Support\Facades\DB;

class FundraiserService
{
    public function getFundraiserCategoryData()
    {
        $actionCategories = Action::select([
                DB::raw("TRIM(action_solicitor_list) as fundraiser"),
                'action_category',
                DB::raw('COUNT(*) as total')
            ])
            ->whereNotNull('action_solicitor_list')
            ->whereNotNull('action_category')
            ->groupBy('fundraiser', 'action_category')
            ->get()
            ->groupBy('fundraiser');

        $fundraisersByCategory = $actionCategories->keys();
        $categories = ['Email', 'Mailing', 'Meeting', 'Phone Call'];
        $chartData = [];

        foreach ($categories as $category) {
            $chartData[] = [
                'label'           => $category,
                'data'            => $fundraisersByCategory->map(function ($fundraiser) use ($actionCategories, $category) {
                    return $actionCategories[$fundraiser]->firstWhere('action_category', $category)?->total ?? 0;
                })->toArray(),
                'backgroundColor' => $this->getCategoryColor($category)
            ];
        }

        return [
            'labels' => $fundraisersByCategory,
            'chart'  => $chartData
        ];
    }

    public function getFundraiserTypeData()
    {
        $actionTypes = Action::select([
                DB::raw("TRIM(action_solicitor_list) as fundraiser"),
                'action_type',
                DB::raw('COUNT(*) as total')
            ])
            ->whereNotNull('action_solicitor_list')
            ->whereNotNull('action_type')
            ->groupBy('fundraiser', 'action_type')
            ->get()
            ->groupBy('fundraiser');

        $fundraisersByType = $actionTypes->keys();
        $types = [
            'Cultivation',
            'Meaningful Move',
            'Other',
            'Qualification',
            'Report Back',
            'Solicitation',
            'Stewardship',
        ];
        $chartData = [];

        foreach ($types as $type) {
            $chartData[] = [
                'label'           => $type,
                'data'            => $fundraisersByType->map(function ($fundraiser) use ($actionTypes, $type) {
                    return $actionTypes[$fundraiser]->firstWhere('action_type', $type)?->total ?? 0;
                })->toArray(),
                'backgroundColor' => $this->getTypeColor($type)
            ];
        }

        return [
            'labels' => $fundraisersByType,
            'chart'  => $chartData
        ];
    }

    private function getCategoryColor($category)
    {
        return match ($category) {
            'Email'      => '#42A5F5',
            'Mailing'    => '#FFCA28',
            'Meeting'    => '#66BB6A',
            'Phone Call' => '#AB47BC',
            default      => '#ccc',
        };
    }

    private function getTypeColor($type)
    {
        return match ($type) {
            'Cultivation'     => '#BA68C8',
            'Meaningful Move' => '#81C784',
            'Other'           => '#E0E0E0',
            'Qualification'   => '#7986CB',
            'Report Back'     => '#AED581',
            'Solicitation'    => '#FF8A65',
            'Stewardship'     => '#66BB6A',
            default           => '#ccc',
        };
    }
}