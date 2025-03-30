<h1 class="text-2xl font-bold">Dashboard</h1>

@if (Auth::check())
    <p>✅ You are logged in as user ID: {{ Auth::id() }}</p>
@else
    <p>❌ Not logged in</p>
@endif