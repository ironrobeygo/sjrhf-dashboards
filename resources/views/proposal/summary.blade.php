<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="min-h-screen flex items-start justify-center px-4 py-10">
        <div class="w-full max-w-6xl">
            <!-- Page Title -->
            <h1 class="text-2xl font-bold mb-6">{{ $title }}</h1>

            @if($records->count())
                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full text-xs text-left">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-3">Constituent ID</th>
                                <th class="px-4 py-3">Name / Organization</th>
                                <th class="px-4 py-3">Proposal Status</th>
                                <th class="px-4 py-3">Proposal Name</th>
                                <th class="px-4 py-3">Solicitors</th>
                                <th class="px-4 py-3 text-center">Target Ask</th>
                                <th class="px-4 py-3 text-center">Expected</th>
                                <th class="px-4 py-3 text-center">Funded</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 divide-y divide-gray-200">
                            @foreach($records as $op)
                            <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.open('https://host.nxt.blackbaud.com/constituent/records/{{ $op->record_id }}','_blank')">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $op->constituent_id }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $op->name ?? $op->organization_name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $op->proposal_status }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $op->proposal_name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $op->solicitors }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">${{ number_format($op->target_ask, 2) }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">${{ number_format($op->amount_expected, 2) }}</td>
                                    <td class="px-4 py-2 text-center whitespace-nowrap">${{ number_format($op->amount_funded, 2) }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Links -->
                <div class="mt-4">
                    {{ $records->links() }}
                </div>
            @else
                <p class="text-gray-600 mt-4">No records found.</p>
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