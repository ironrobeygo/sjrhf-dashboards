<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\DataUploadService;

class DataController extends Controller
{
    protected $dataUploadService;

    public function __construct(DataUploadService $dataUploadService)
    {
        $this->dataUploadService = $dataUploadService;
    }

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

        try {
            $this->dataUploadService->processCSV($file->getRealPath());
        } catch (\Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }

        return redirect()->route('dashboard')->with('success', 'CSV uploaded and processed.');
    }
}