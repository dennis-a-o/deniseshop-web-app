<x-mail::message>
Hi {{ $order->customer_name }},

<p style="color: #6600cc;">
	Unfortunately, your order <b>{{ $order->code }}</b> has been cancelled.<br><br>
	If you hade made apayment for your order, a refund will validated immidiately and have it processed as per our refund policy and timelines.
	<br><br>
	Thank you for shopping on <b>{{ config('app.name') }}</b>. We look forward to serving you again.
</p>

The following items were cancelled:
<table class="table-custom">
	<thead>
		<tr>
			<th>Item</th>
			<th>Quantity</th>
			<th>Price</th>
		</tr>
	</thead>
	<tbody>
		@foreach($order->orderItem as $item)
		<tr>
			<td>
				<div style="display: flex; align-content: center;">
					<img src="{{ url('/assets/img/products/') }}/{{ $item->product->image }}" width="40" height="40">&nbsp;&nbsp;&nbsp;
					<span>{{ $item->product->name }}</span> 
				</div>
			</td>
			<td>{{ $item->quantity }}</td>
			<td>{{ Util::currencySymbol() }}{{ $item->total_price }}</td>
		</tr>
		@endforeach
	</tbody>
</table>

<br>

Happy shopping!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>