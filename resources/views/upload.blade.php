@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100 flex items-center justify-center px-4">
    <div class="max-w-xl w-full bg-white p-8 rounded-2xl shadow">
        <h2 class="text-2xl font-bold text-gray-800 mb-6">Upload Opportunities CSV</h2>

        <p class="text-gray-600 mb-4">
            No data was found in the system. Please upload the <strong>opportunities.csv</strong> file to continue.
        </p>

        <form method="POST" action="#" enctype="multipart/form-data" class="space-y-6">
            {{-- CSRF if needed --}}
            @csrf

            <div>
                <label for="csv_file" class="block text-sm font-medium text-gray-700 mb-1">CSV File</label>
                <input type="file" name="csv_file" id="csv_file" accept=".csv"
                    class="block w-full border border-gray-300 rounded-lg shadow-sm text-sm focus:ring focus:ring-indigo-200 focus:border-indigo-300">
            </div>

            <div class="flex justify-end">
                <button type="submit"
                    class="bg-indigo-600 hover:bg-indigo-700 text-white font-medium px-4 py-2 rounded-lg shadow">
                    Upload
                </button>
            </div>
        </form>
    </div>
</div>
@endsection