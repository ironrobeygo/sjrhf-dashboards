<!DOCTYPE html>
<html>
<head>
    <title>Funded Opportunities Details</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex items-start justify-center px-4 py-10">
        <div class="w-full max-w-6xl">
            <!-- Page Title -->
            <h1 class="text-2xl font-bold mb-2">Funded Opportunities Details</h1>
            <p class="text-sm text-gray-600 mb-6">
                Fiscal Year: {{ $fiscalStart->format('M d, Y') }} – {{ $fiscalEnd->format('M d, Y') }}
            </p>

            @if($fundedOpportunities->count())
                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-3">Constituent ID</th>
                                <th class="px-4 py-3">Name / Organization</th>
                                <th class="px-4 py-3">Proposal Name</th>
                                <th class="px-4 py-3">Date Closed</th>
                                <th class="px-4 py-3 text-right">Amount Funded</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 divide-y divide-gray-200">
                            @foreach($fundedOpportunities as $opportunity)
                                <tr class="hover:bg-gray-50 transition cursor-pointer">
                                    <td class="px-4 py-2">{{ $opportunity->constituent_id }}</td>
                                    <td class="px-4 py-2">{{ $opportunity->name ?? $opportunity->organization_name }}</td>
                                    <td class="px-4 py-2">{{ $opportunity->proposal_name }}</td>
                                    <td class="px-4 py-2">{{ \Carbon\Carbon::parse($opportunity->date_closed)->format('M d, Y') }}</td>
                                    <td class="px-4 py-2 text-right">${{ number_format($opportunity->amount_funded, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-600">No funded opportunities found for this fiscal year.</p>
            @endif

            <!-- Back link -->
            <div class="mt-6">
                <a href="{{ route('dashboard') }}" class="inline-block text-blue-600 hover:underline text-sm">
                    ← Back to Dashboard
                </a>
            </div>
        </div>
    </div>
</body>
</html>