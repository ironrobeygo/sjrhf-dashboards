<!DOCTYPE html>
<html>
<head>
    <title>Upload CSV</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
</head>
<body class="bg-gray-100 text-gray-900 antialiased">
    <div class="min-h-screen flex items-center justify-center px-4 py-8">
        <div class="w-full max-w-md">
            <h1 class="text-xl font-semibold mb-4">Upload a CSV File</h1>

            <form action="{{ route('upload.store') }}" method="POST" enctype="multipart/form-data" class="bg-white p-5 rounded-lg shadow">
                @csrf
                <div class="mb-4">
                    <label class="block text-sm font-medium mb-1" for="csv">Select File</label>
                    <input type="file" name="csv" id="csv" required class="w-full p-2 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded transition">
                    Upload CSV
                </button>
            </form>

            <div class="mt-4 text-sm">
                <a class="text-blue-600 hover:underline" href="{{ route('dashboard') }}">← Back to Dashboard</a>
            </div>
        </div>
    </div>
</body>
</html>