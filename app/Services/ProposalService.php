<?php

namespace App\Services;

use App\Models\Opportunity;

class ProposalService
{
    protected $statusMapping = [
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

    /**
     * Retrieve proposals based on group criteria.
     *
     * @param string $group
     * @return array
     */
    public function getProposalsByGroup(string $group, int $perPage = 15): array
    {
        $proposals = Opportunity::openProposals()
            ->where('key_indicator', $group)
            ->paginate($perPage);

        return [
            'proposals' => $proposals,
            'group'     => $group
        ];
    }

    /**
     * Retrieve summary of active proposals by status.
     *
     * @param string $status
     * @return array
     */
    public function getProposalSummary(string $status, int $perPage = 15): array
    {
        $statusFull = $this->statusMapping[$status] ?? null;
        if (!$statusFull) {
            abort(404, 'Status not found.');
        }

        $records = Opportunity::active()
            ->where('proposal_status', $statusFull)
            ->paginate($perPage);

        $title = "Active Proposals with Status: $statusFull";

        return compact('records', 'title');
    }
}