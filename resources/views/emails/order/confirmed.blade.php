<x-mail::message>
Hi {{ $order->first_name }},

Thank you for shopping on {{ config('app.name') }}

Your order <b>{{ $order->code }}</b> has been confirmed successfully.
@if(order->downloadable)
It will be packed nad shipped as soon as possible. You will receive a notification from us once the 
order is available for collection or delivery from <b>{{ $order->pickup_location }}</b>.
@endif

You ordered:
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
		<tr>
			<td colspan="2" style="text-align: right;"><b>Shipping</b></td>
			<td>{{ Util::currencySymbol() }}{{ $order->shipping_amount }}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right;"><b>Discount</b></td>
			<td>{{ Util::currencySymbol() }}{{ $order->discount_amount ?? 0 }}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right;"><b>Total</b></td>
			<td>{{ Util::currencySymbol() }}{{ $order->amount }}</td>
		</tr>
		<tr>
			<td colspan="2" style="text-align: right;"><b>Payment method</b></td>
			<td>{{ $order->payment_method }}</td>
		</tr>
	</tbody>
</table>

<br>

Happy shopping!

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
