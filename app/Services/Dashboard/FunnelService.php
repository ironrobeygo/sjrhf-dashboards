<?php

namespace App\Services\Dashboard;

use App\Models\Opportunity;
use Carbon\Carbon;

class FunnelService
{
    public function getFunnelData(Carbon $cutoff, Carbon $now)
    {
        return [
            'Target Ask' => Opportunity::active()
                ->whereBetween('date_asked', [$cutoff, $now])
                ->sum('target_ask'),
            'Expected'   => Opportunity::active()
                ->whereBetween('date_expected', [$cutoff, $now])
                ->sum('amount_expected'),
            'Funded'     => Opportunity::active()
                ->whereBetween('date_closed', [$cutoff, $now])
                ->sum('amount_funded'),
        ];
    }
}