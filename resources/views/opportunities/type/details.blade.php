<!DOCTYPE html>
<html>
<head>
    <title>{{ $title }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="min-h-screen flex items-start justify-center px-4 py-10">
        <div class="w-full max-w-6xl">
            <!-- Page Title -->
            <h1 class="text-2xl font-bold mb-6">{{ $title }} <span class="text-sm text-gray-500">(Past 12 Months)</span></h1>

            @if($records->count())
                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full text-sm text-left">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-3">Constituent ID</th>
                                <th class="px-4 py-3">Name / Organization</th>
                                <th class="px-4 py-3">Solicitors</th>
                                <th class="px-4 py-3 text-center">Amount</th>
                                <th class="px-4 py-3 text-center">Date</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 divide-y divide-gray-200">
                            @foreach($records as $op)
                            <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.open('https://host.nxt.blackbaud.com/constituent/records/{{ $op->record_id }}','_blank')">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $op->constituent_id }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $op->name ?? $op->organization_name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $op->solicitors }}</td>

                                    @if($title === 'Target Ask Opportunities')
                                        <td class="px-4 py-2 text-center whitespace-nowrap">${{ number_format($op->target_ask, 2) }}</td>
                                        <td class="px-4 py-2 text-center whitespace-nowrap">{{ \Carbon\Carbon::parse($op->date_asked)->format('M d, Y') }}</td>
                                    @elseif($title === 'Expected Opportunities')
                                        <td class="px-4 py-2 text-center whitespace-nowrap">${{ number_format($op->amount_expected, 2) }}</td>
                                        <td class="px-4 py-2 text-center whitespace-nowrap">{{ \Carbon\Carbon::parse($op->date_expected)->format('M d, Y') }}</td>
                                    @elseif($title === 'Funded Opportunities')
                                        <td class="px-4 py-2 text-center whitespace-nowrap">${{ number_format($op->amount_funded, 2) }}</td>
                                        <td class="px-4 py-2 text-center whitespace-nowrap">{{ \Carbon\Carbon::parse($op->date_closed)->format('M d, Y') }}</td>
                                    @else
                                        <td class="px-4 py-2 text-center whitespace-nowrap">-</td>
                                        <td class="px-4 py-2 text-center whitespace-nowrap">-</td>
                                    @endif
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
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