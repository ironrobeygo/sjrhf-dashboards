<?php

namespace App\Services;

use App\Models\Opportunity;
use App\Models\Action;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class DataUploadService
{
    /**
     * Process the CSV file based on its headers.
     *
     * @param string $filePath
     * @return void
     *
     * @throws \Exception
     */
    public function processCSV(string $filePath): void
    {
        // Read file into an array of rows
        $data = array_map('str_getcsv', file($filePath));
        // Standardize headers by trimming, lowering case, and replacing spaces with underscores
        $headers = array_map(fn($h) => str_replace(' ', '_', strtolower(trim($h))), array_shift($data));
        $rows = collect($data);
        $headerLine = implode(',', $headers);

        // Determine file type by inspecting headers
        if (str_contains($headerLine, 'proposal_name') || str_contains($headerLine, 'prospect_proposal_name')) {
            $this->handleOpportunities($rows);
        } elseif (str_contains($headerLine, 'action_system_record_id')) {
            $this->handleActions($rows);
        } else {
            throw new \Exception('Could not determine file type from headers.');
        }
    }

    /**
     * Parse a date string into a Carbon instance.
     *
     * @param string|null $value
     * @return Carbon|null
     */
    protected function parseDate(?string $value): ?Carbon
    {
        $clean = trim($value ?? '');
        if (!$clean) {
            return null;
        }
        // For example, uncomment and adjust the format as needed:
        // return Carbon::createFromFormat('d/m/Y', $clean);
        return null;
    }

    /**
     * Parse a currency string into a float.
     *
     * @param string|null $value
     * @return float
     */
    protected function parseCurrency(?string $value): float
    {
        return (float) str_replace(['$', ','], '', $value ?? '0');
    }

    /**
     * Handle CSV rows for Opportunities.
     *
     * @param Collection $rows
     * @return void
     */
    protected function handleOpportunities(Collection $rows): void
    {
        // Remove existing opportunities before import
        Opportunity::truncate();

        foreach ($rows as $row) {
            Opportunity::create([
                'constituent_id'          => $row[0] ?? null,
                'name'                    => $row[1] ?? null,
                'organization_name'       => $row[2] ?? null,
                'key_indicator'           => $row[3] ?? null,
                'solicitors'              => $row[4] ?? null,
                'assigned_solicitor_type' => $row[5] ?? null,
                'prospect_status'         => $row[6] ?? null,
                'proposal_status'         => $row[7] ?? null,
                'proposal_name'           => $row[8] ?? null,
                'fund'                    => $row[9] ?? null,
                'purpose'                 => $row[10] ?? null,
                'date_added'              => $row[11] ?? '',
                'target_ask'              => $this->parseCurrency($row[12] ?? '0'),
                'date_asked'              => $row[13] ?? '',
                'amount_expected'         => $this->parseCurrency($row[14] ?? '0'),
                'date_expected'           => $row[15] ?? '',
                'amount_funded'           => $this->parseCurrency($row[16] ?? '0'),
                'date_closed'             => $row[17] ?? '',
                'deadline'                => $row[18] ?? '',
                'is_inactive'             => strtolower(trim($row[19] ?? '')) === 'yes',
                'record_id'               => $row[20] ?? null,
            ]);
        }
    }

    /**
     * Handle CSV rows for Actions.
     *
     * @param Collection $rows
     * @return void
     */
    protected function handleActions(Collection $rows): void
    {
        Action::truncate();

        // Process rows in chunks for better performance
        $rows->chunk(100)->each(function (Collection $chunk) {
            foreach ($chunk as $row) {
                Action::create([
                    'action_system_record_id' => $row[0] ?? null,
                    'action_category'         => $row[1] ?? null,
                    'action_completed_on'     => $row[2] ?? '',
                    'action_solicitor_list'   => $row[3] ?? null,
                    'action_type'             => $row[4] ?? null,
                    'constituent_id'          => $row[5] ?? null,
                    'name'                    => $row[6] ?? null,
                    'record_id'               => $row[7] ?? null,
                ]);
            }
        });
    }
}