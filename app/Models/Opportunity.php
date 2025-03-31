<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Opportunity extends Model
{
    public const OPEN_STATUSES = [
        'Solicited - Ask Made',
        'Cultivation',
        'Qualification',
        'Verbal Agreement',
        'Pre-Ask',
    ];

    protected $fillable = [
        'constituent_id',
        'name',
        'organization_name',
        'key_indicator',
        'solicitors',
        'assigned_solicitor_type',
        'prospect_status',
        'proposal_status',
        'proposal_name',
        'fund',
        'purpose',
        'date_added',
        'target_ask',
        'date_asked',
        'amount_expected',
        'date_expected',
        'amount_funded',
        'date_closed',
        'deadline',
        'is_inactive',
        'record_id'
    ];

    public function scopeActive($query){
        return $query->where('is_inactive', false);
    }

    public function scopeOpenProposals($query)
    {
        return $query->active()->whereIn('proposal_status', self::OPEN_STATUSES);
    }

    public static function getActiveProposals(){
        return self::active()->get()->groupBy('proposal_status');
    }

    public function scopeFundedClosed($query, $start, $end){
        return $query->active()
                ->where('proposal_status', 'Funded/Closed')
                ->whereNotNull('date_closed')
                ->whereBetween('date_closed', [$start, $end]);
    }

    public function scopeProposalStatus($query, $status, $start, $end){
        return $query->active()
            ->where('proposal_status', $status)
            ->whereBetween('date_asked', [$start, $end]);
    }
}
