<!DOCTYPE html>
<html>
<head>
    <title>Open Opportunities - {{ $purpose }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex items-start justify-center px-4 py-10">
        <div class="w-full max-w-6xl">
            <!-- Page Title -->
            <h1 class="text-2xl font-bold mb-4">Open Opportunities – {{ $purpose }}</h1>

            @if($opportunities->count())
                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-3">Constituent ID</th>
                                <th class="px-4 py-3">Name / Organization</th>
                                <th class="px-4 py-3">Proposal Name</th>
                                <th class="px-4 py-3">Purpose</th>
                                <th class="px-4 py-3 text-right">Target Ask</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 divide-y divide-gray-200">
                            @foreach($opportunities as $opportunity)
                            <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.open('https://host.nxt.blackbaud.com/constituent/records/{{ $opportunity->record_id }}','_blank')">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $opportunity->constituent_id }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $opportunity->name ?? $opportunity->organization_name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $opportunity->proposal_name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $opportunity->purpose }}</td>
                                    <td class="px-4 py-2 text-right whitespace-nowrap">${{ number_format($opportunity->target_ask, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-gray-600 mt-4">No open opportunities found for <strong>{{ $purpose }}</strong>.</p>
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