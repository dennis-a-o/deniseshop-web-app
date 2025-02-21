<x-mail::message>

{{ $detail['name'] }}

{{ $detail['message'] }}

<x-mail::button :url="$detail['url']">
SET PASSWORD
</x-mail::button>

Button not working for you? Copy the link below into your browser.

{{ $detail['url'] }}

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
