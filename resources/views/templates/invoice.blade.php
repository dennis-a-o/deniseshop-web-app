<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<title>Invoice</title>
	<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700&family=Open+Sans:300,400,500,700">
	<style type="text/css">
		body{
			font-size: 14px;
			font-family: "Open Sans", sans-serif;
			position: relative;
		}
		p,span{
			color: #566176;
		}

		table{
			border-collapse: collapse;
			width: 100%;
		}

		table  tr td{
			padding: 0;
		}

		table tr td:last-child {
            text-align: right
        }

        table thead tr th{
        	color: #566176;
        	border-bottom: 1px solid #67748e;
        }

		.bold,strong,b{
			font-weight: 700;
		}

		small{
			font-size: 80%;
		}

		.invoice-items-container{
			margin: 70px 0;
		}
		.invoice-items-container th{
			border-bottom: 2px solid #dddddd;
			padding: 12px 0;
			color: #b5bcc9;
			text-align: right;
		}

		.invoice-items-container tr th:first-child{
			text-align: left;
		}

		.invoice-items-container td{
			padding-top: 24px;
			color: #566176;
			text-align: right;
		}

		.invoice-items-container td:first-child{
			text-align: left;
		}

		.stamp{
			position: fixed;
			top: 40%;
			left: 40%;
			padding: 12px;
			font-size: 30px;
			font-weight: bolder;
			opacity: 0.3;
			transform: rotate(-24deg);
		}

		.stamp-success{
			border: 2px solid #009900;
			color: #009900;
		}
		.stamp-warning{
			border:2px solid #ff6666;
			color: #ff6666;
		}
		
	</style>
</head>
<body>
	@if($order->payment_status == "completed")
	<span class="stamp stamp-success"> 
		completed
	</span>
	@else
	<span class="stamp stamp-warning"> 
		{{ $order->payment_status }}
	</span>
	@endif

 	<table class="invoice-container">
 		<tr>
 			<td>
 				<div class="logo-section">
 					<img src="{{ url('/assets/img/general/logo_dark.png') }}" alt="{{ config('app.name') }}">
 				</div>
 			</td>
 			<td>
 				<p>
 					<strong>{{ date('F d, Y', strtotime($order->created_at)) }}</strong>
 				</p>
 				<p>
 					<strong>Order ID:</strong>&nbsp;
 					{{ $order->code }}
 				</p>
 			</td>
 		</tr>
 	</table>

 	<table class="invoice-container">
 		<tr>
 			<td>
 				<p>{{ config('app.name') }}</p>
 				<p>
 					{{ $business['address']  }}
 				</p>
 				<p>
 					{{ $business['email']  }}
 				</p>
 				<p>
 					{{ $business['phone']  }}
 				</p>
 			</td>
 			<td>
 				@if($order->orderAddress != null)
 				<p>{{ $order->orderAddress->name }}</p>
 				<p>{{ $order->orderAddress->email }}</p>
 				<p>
 					{{ $order->orderAddress->address }},
 					{{ $order->orderAddress->zip_code }},
 					{{ $order->orderAddress->city }},
 					{{ $order->orderAddress->country }}
 				</p>
 				<p>{{ $order->orderAddress->phone }}</p>
 				@endif
 			</td>
 		</tr>
 	</table>

 	<table class="invoice-items-container">
 		<thead>
 			<tr>
 				<th>Product</th>
 				<th>Option</th>
 				<th>Price</th>
 				<th>Quantity</th>
 				<th>Total</th>
 			</tr>
 		</thead>
 		<tbody>
 			@foreach($order->orderItem as $item)
 			<tr>
 				<td>{{ $item->product->name }}</td>
 				<td> (Color:{{ $item->color }}, Size:{{ $item->size }})</td>
 				<td>{{ Util::currencySymbol() }}{{ $item->price }}</td>
 				<td>{{ $item->quantity }}</td>
 				<td><b>{{ Util::currencySymbol() }}{{ $item->total_price }}</b></td>
 			</tr>
 			@endforeach
 			<tr>
 				<td colspan="4" style="text-align: right;">Total quantity</td>
 				<td><b>{{ $order->quantity }}</b></td>
 			</tr>
 			<tr>
 				<td colspan="4" style="text-align: right;">Subtotal</td>
 				<td><b>{{ Util::currencySymbol() }}{{ $order->sub_total }}</b></td>
 			</tr>
 			<tr>
 				<td colspan="4" style="text-align: right;">Shipping fee</td>
 				<td><b>{{ Util::currencySymbol() }}{{ $order->shipping_amount }}</b></td>
 			</tr>
 		</tbody>
 	</table>

 	<table class="invoice-items-container">
 		<thead>
 			<tr>
 				<th>Payment Info</th>
 				<th>Total Amount</th>
 			</tr>
 		</thead>
 		<tbody>
 			<tr>
 				<td>
 					<p>Payment method: <b>{{ $order->payment_method }}</b></p>
 					<p>Payment status: <b>{{  $order->payment_status }}</b></p>
 				</td>
 				<td>
 					<span style="font-size: 20px; color:#ff6666;">{{ Util::currencySymbol() }}{{ $order->amount }}</span>
 				</td>
 			</tr>
 		</tbody>
 	</table>
</body>
</html>