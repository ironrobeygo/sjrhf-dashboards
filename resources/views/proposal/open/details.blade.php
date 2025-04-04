<!DOCTYPE html>
<html>
<head>
    <title>Open Proposals – {{ ucfirst($group) }}</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex items-start justify-center px-4 py-10">
        <div class="w-full max-w-6xl">
            <!-- Page Header -->
            <h1 class="text-2xl font-bold mb-4">Open Proposals for {{ ucfirst($group) }}</h1>

            @if($proposals->count())
                <div class="bg-white rounded-lg shadow overflow-x-auto">
                    <table class="min-w-full text-xs text-left">
                        <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                            <tr>
                                <th class="px-4 py-3">Constituent ID</th>
                                <th class="px-4 py-3">Name / Organization</th>
                                <th class="px-4 py-3">Proposal Status</th>
                                <th class="px-4 py-3">Proposal Name</th>
                            </tr>
                        </thead>
                        <tbody class="text-gray-800 divide-y divide-gray-200">
                            @foreach($proposals as $proposal)
                                <tr class="hover:bg-gray-50 transition">
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $proposal->constituent_id }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $proposal->name ?? $proposal->organization_name }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $proposal->proposal_status }}</td>
                                    <td class="px-4 py-2 whitespace-nowrap">{{ $proposal->proposal_name }}</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <!-- Pagination Links -->
                <div class="mt-4">
                    {{ $opportunities->links() }}
                </div>
            @else
                <p class="text-gray-600 mt-4">No open proposals found for <strong>{{ ucfirst($group) }}</strong>.</p>
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