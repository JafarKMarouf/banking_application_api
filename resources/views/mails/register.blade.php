<!-- <x-mail::message>
    Welcome {{ $name }},

    please verify Your Email using this code {{ $otp }}

    This code is valid for 2 minutes.

    Thank you for registeration in our system.
    {{ config('app.name') }}
</x-mail::message> -->
<x-mail::message>
    <div style="text-align: center; padding: 20px;">
        <h1 style="color: #4A90E2;">Welcome, {{ $name }}!</h1>

        <p style="font-size: 18px; line-height: 1.5;">
            Please verify your email using the code below:
        </p>

        <h2 style="font-size: 24px; font-weight: bold; color: #E94E77;">
            {{ $otp }}
        </h2>

        <p style="font-size: 16px; line-height: 1.5;">
            This code is valid for <strong>2 minutes</strong>.
        </p>

        <p style="font-size: 16px; line-height: 1.5;">
            Thank you for registering in our system!
        </p>

        <footer style="margin-top: 20px; font-size: 14px; color: #888;">
            {{ config('app.name') }} &copy; {{ date('Y') }}
        </footer>
    </div>
</x-mail::message>
