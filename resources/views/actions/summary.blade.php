<!DOCTYPE html>
<html>
<head>
    <title>Funded Opportunities Details</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    @vite('resources/css/app.css')
</head>
<body class="bg-gray-50 text-gray-800 antialiased">
    <div class="min-h-screen flex items-start justify-center px-4 py-10">
        <div class="w-full max-w-6xl">
            <!-- Page Title -->
            <h1 class="text-xl font-bold mb-4">
                Actions for {{ $fundraiser }} – {{ $category }}
            </h1>

            @if($actions->isEmpty())
                <p class="text-gray-600">No actions found.</p>
            @else
            <div class="bg-white rounded-lg shadow overflow-x-auto">
                <table class="min-w-full text-xs text-left">
                    <thead class="bg-gray-100 text-xs text-gray-600 uppercase">
                        <tr>
                            <th class="py-3 px-4">Date</th>
                            <th class="py-3 px-4">Constituent ID</th>
                            <th class="py-3 px-4">Name</th>
                            <th class="py-3 px-4">Type</th>
                            <th class="py-3 px-4">Category</th>
                        </tr>
                    </thead>
                    <tbody class="text-gray-800 divide-y divide-gray-200">
                        @foreach($actions as $action)
                        <tr class="hover:bg-gray-50 transition cursor-pointer" onclick="window.open('https://host.nxt.blackbaud.com/constituent/records/{{ $action->record_id }}','_blank')">
                                <td class="py-2 px-4">{{ \Carbon\Carbon::parse($action->action_completed_on)->format('M d, Y') }}</td>
                                <td class="py-2 px-4">{{ $action->constituent_id }}</td>
                                <td class="py-2 px-4">{{ $action->name }}</td>
                                <td class="py-2 px-4">{{ $action->action_type }}</td>
                                <td class="py-2 px-4">{{ $action->action_category }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif

            <p class="mt-4">
                <a href="{{ route('dashboard') }}" class="text-blue-500 hover:underline">← Back to Dashboard</a>
            </p>
        </div>
    </div>
</body>
</html>