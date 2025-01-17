<x-mail::message>
    Welcome {{ $name }},

    please verify Your Email using this code {{ $otp }}

    This code is valid for 2 minutes.

    Thank you for registeration in our system.
    {{ config('app.name') }}
</x-mail::message>
