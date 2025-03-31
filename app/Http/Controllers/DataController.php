<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Opportunity;
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
        $headers = array_shift($data);

        // Convert array to collection for easier iteration
        $rows = collect($data);

        // Helper to parse currency strings
        $parseCurrency = function ($value) {
            return (float) str_replace(["$", ","], "", $value);
        };

        // More lenient date parser that skips invalid/empty fields
        // Force parse date as DD/MM/YYYY
        $parseDate = function ($dateString) {
            $clean = trim($dateString);
            if (!$clean) return null;

            try {
                // This will parse day first, then month, then year
                return \Carbon\Carbon::createFromFormat('d/m/Y', $clean);
            } catch (\Exception $e) {
                return null;
            }
        };


        // Clear old data
        Opportunity::truncate();

        // Insert new rows
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
                'date_added' => $parseDate($row[11] ?? ''),
                'target_ask' => $parseCurrency($row[12] ?? '0'),
                'date_asked' => $parseDate($row[13] ?? ''),
                'amount_expected' => $parseCurrency($row[14] ?? '0'),
                'date_expected' => $parseDate($row[15] ?? ''),
                'amount_funded' => $parseCurrency($row[16] ?? '0'),
                'date_closed' => $parseDate($row[17] ?? ''),
                'deadline' => $parseDate($row[18] ?? ''),
                'is_inactive' => strtolower(trim($row[19] ?? '')) === 'yes',
                'record_id' => $row[20] ?? null,
            ]);
        }

        // After storing, redirect back to the dashboard
        return redirect()->route('dashboard');
    }
}
