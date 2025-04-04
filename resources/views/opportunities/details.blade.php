@extends('layouts.app')

@section('title', 'Open Opportunities')

@section('content')
    <div class="min-h-screen flex items-start justify-center px-4 py-8">
        <div class="w-full max-w-6xl">
            <!-- Page Title -->
            <h1 class="text-2xl font-bold mb-6">{{ $title }}</h1>

            @if($opportunities->count())
                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full text-xs text-left">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-3">Constituent ID</th>
                                <th class="px-4 py-3">Name / Organization</th>
                                <th class="px-4 py-3">Proposal Name</th>
                                <th class="px-4 py-3">Status</th>
                                <th class="px-4 py-3 text-right">Amount</th>
                                <th class="px-4 py-3 text-right">Date Closed</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-700 divide-y divide-gray-200">
                            @foreach($opportunities as $opportunity)
                            <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.open('https://host.nxt.blackbaud.com/constituent/records/{{ $opportunity->record_id }}','_blank')">
                                    <td class="px-4 py-2">{{ $opportunity->constituent_id }}</td>
                                    <td class="px-4 py-2">{{ $opportunity->name ?? $opportunity->organization_name }}</td>
                                    <td class="px-4 py-2">{{ $opportunity->proposal_name }}</td>
                                    <td class="px-4 py-2">{{ $opportunity->proposal_status }}</td>
                                    <td class="px-4 py-2 text-right">
                                        @if($opportunity->proposal_status == 'Funded/Closed')
                                            ${{ number_format($opportunity->amount_funded, 2) }}
                                        @else
                                            ${{ number_format($opportunity->target_ask, 2) }}
                                        @endif
                                    </td>
                                    <td class="px-4 py-2 text-right">{{ $opportunity->date_closed }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

            @else
                <p class="text-gray-500">No opportunities found.</p>
            @endif

            <!-- Back Link -->
            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="inline-block text-blue-600 hover:underline text-sm">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>
@endsection