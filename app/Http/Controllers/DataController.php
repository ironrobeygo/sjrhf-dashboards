<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;
use App\Models\Action;
use Carbon\Carbon;

class DataController extends Controller
{
    public function uploadForm()
    {
        return view('upload');
    }

    public function upload(Request $request)
    {
        $request->validate([
            'csv' => 'required|file|mimes:csv,txt'
        ]);

        $file = $request->file('csv');
        $data = array_map('str_getcsv', file($file->getRealPath()));

        $headers = array_map(fn($h) => str_replace(' ', '_', strtolower(trim($h))), array_shift($data));
        $rows = collect($data);
        $headerLine = implode(',', $headers);

        if (str_contains($headerLine, 'proposal_name') || str_contains($headerLine, 'prospect_proposal_name')) {
            $this->handleOpportunities($rows);
        } elseif (str_contains($headerLine, 'action_system_record_id')) {
            $this->handleActions($rows);
        } else {
            return redirect()->back()->with('error', 'Could not determine file type from headers.');
        }

        return redirect()->route('dashboard')->with('success', 'CSV uploaded and processed.');
    }

    private function parseDate(?string $value): ?Carbon
    {
        
        $clean = trim($value ?? '');
        if (!$clean) return null;

        // return Carbon::createFromFormat('d/m/Y', $clean);
        // dd($value);
        // try {
        //     return Carbon::createFromFormat('d/m/Y', $clean);
        // } catch (\Exception $e) {
        //     return null;
        // }
    }

    private function parseCurrency(?string $value): float
    {
        return (float) str_replace(['$', ','], '', $value ?? '0');
    }

    private function handleOpportunities($rows): void
    {
        Opportunity::truncate();

        foreach ($rows as $row) {
            Opportunity::create([
                'constituent_id' => $row[0] ?? null,
                'name' => $row[1] ?? null,
                'organization_name' => $row[2] ?? null,
                'key_indicator' => $row[3] ?? null,
                'solicitors' => $row[4] ?? null,
                'assigned_solicitor_type' => $row[5] ?? null,
                'prospect_status' => $row[6] ?? null,
                'proposal_status' => $row[7] ?? null,
                'proposal_name' => $row[8] ?? null,
                'fund' => $row[9] ?? null,
                'purpose' => $row[10] ?? null,
                'date_added' => $row[11] ?? '',
                'target_ask' => $this->parseCurrency($row[12] ?? '0'),
                'date_asked' => $row[13] ?? '',
                'amount_expected' => $this->parseCurrency($row[14] ?? '0'),
                'date_expected' => $row[15] ?? '',
                'amount_funded' => $this->parseCurrency($row[16] ?? '0'),
                'date_closed' => $row[17] ?? '',
                'deadline' => $row[18] ?? '',
                'is_inactive' => strtolower(trim($row[19] ?? '')) === 'yes',
                'record_id' => $row[20] ?? null,
            ]);
        }
    }

    private function handleActions($rows): void
    {
        Action::truncate();

        $rows->chunk(100)->each(function ($chunk) {
            foreach ($chunk as $row) {
                Action::create([
                    'action_system_record_id' => $row[0] ?? null,
                    'action_category' => $row[1] ?? null,
                    'action_completed_on' => $row[2] ?? '',
                    'action_solicitor_list' => $row[3] ?? null,
                    'action_type' => $row[4] ?? null,
                    'constituent_id' => $row[5] ?? null,
                    'name' => $row[6] ?? null,
                    'record_id' => $row[7] ?? null,
                ]);
            }
        });
    }
}