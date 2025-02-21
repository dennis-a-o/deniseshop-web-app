<x-mail::message>
Dear {{ $payment->user->first_name }} {{ $payment->user->last_name }}

Your payment of <b>{{ Util::currencySymbol() }}{{ $payment->amount }}</b> to <b>{{ config('app.name') }}</b> has been completed.

Amount of the transaction <b>{{ Util::currencySymbol() }}{{ $payment->amount }}</b> of which paid with your  {{ $payment->payment_channel }}.

Reference Number: <b>{{ $payment->transaction_id }}</b>

If you have any questions about this payment, please fill our Contact-Us form.
To make things easier, please include the reference number: <b>{{ $payment->transaction_id }}</b>

Happy shopping!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
