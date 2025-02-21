<x-mail::message>
Hi {{ $order->name }},

Your item(s) from your order <b>{{ $order->code }}</b> has been shipped. You will be notified when it's out for delivery.

This package contains:
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
