<x-mail::message>
    <h1>Welcome, {{ $username }}!</h1>
    <p>Thank you for join to our platform :) </p>
    <footer>
        {{ config('app.name') }} &copy; {{ date('Y') }}
    </footer>
</x-mail::message>
