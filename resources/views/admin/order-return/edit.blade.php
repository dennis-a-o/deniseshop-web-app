@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Edit order return</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/order-returns') }}">Order returns</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Edit order returns</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="row">
		<div class="col-lg-9">
			<div class="card border-0 rounded-4 p-4">
				<h5>Order information</h5>
				<div class="mt-4">
					<table class="table">
						<tbody>
							@foreach($items as $item)
							<tr>
								<td>
									<img class="rounded-2 shadow-sm" src="{{ url('/assets/img/products').'/'.$item->product_image }}" width="40" height="40">
								</td>
								<td>
									<a href="{{ url('/admin/product/edit').'/'.$item->product_id }}">{{ $item->product_name }}</a>
									<p>(Size: {{ $item->size }},Color: {{ $item->color}})</p>
									<p>SKU: <span class="text-uppercase">{{ $item->sku }}</span></p>
								</td>
								<td>{{ Util::currencySymbol().$item->price }}</td>
								<td>x</td>
								<td>{{ $item->qty }}</td>
								<td>{{ Util::currencySymbol().$item->price*$item->qty }}</td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="row">
					<div class="col-lg-6">
					</div>
					<div class="col-lg-6">
						<table class="table table-borderless">
							<tbody>
								<tr>
									<td><span>Total return amount:</span></td>
									<td><span>{{ Util::currencySymbol().$order->payment_amount }}</span></td>
								</tr>
								<tr>
									<td><span>Status:</span></td>
									<td>
										@switch($order->return_status)
											@case("pending")
											<span class="badge badge-danger">pending</span>
											@break
											@case("confirmed")
											@case("processing")
											<span class="badge badge-info">{{ $order->status }}</span>
											@break
											@case("completed")
											<span class="badge badge-info">completed</span>
											@break
											@case("cancelled")
											<span class="badge badge-warning">{{ $order->status }}</span>
											@break
										@endswitch
									</td>
								</tr>
							</tbody>
						</table>
					</div>
				</div>
			</div>
			<div class="card border-0 rounded-4 p-4 mt-4">
				<h5>Return order status</h5>
				<form method="post" id="status-form">
					<div class="row">
						<div class="col-12">
							@csrf
							<input type="hidden" name="id[]" value="{{ $order->id }}">
							<label class="form-label mt-4">Status</label>
							<select class="form-select" name="status">
								<option value="pending">Pending</option>
								<option value="processing" @if($order->return_status =="processing") selected @endif>Processing</option>
								<option value="completed"@if($order->return_status =="completed") selected @endif>Completed</option>
								<option value="cancelled" @if($order->return_status =="cancelled") selected @endif>Cancelled</option>
							</select>
						</div>
						<div class="col-12">
							<button class="btn btn-primary btn-sm mt-4">update</button>
						</div>
					</div>
				</form>
			</div>
		</div>
		<div class="col-lg-3">
			<div class="card border-0 rounded-4 p-4 mt-4 mt-lg-0">
				<h5>Customer</h5>
				<div class="row">
					<div class="col-12 ">
						<div class="d-flex mt-4">
							<img class="rounded-circle shadow-sm me-4" src="{{ url('/assets/img/users').'/'.$order->customer_image }}" width="45" height="45">
							<div class="overflow-auto">
								<a href="{{ url('/admin/user/edit').'/'.$order->user_id }}"><h6>{{ $order->customer_name }}</h6></a>
								<span class="fw-bold">Email: </span><a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a><br>
								<span class="fw-bold">Phone: </span><a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone }}</a>
							</div>
						</div>
						
					</div>
					<div class="col-12 border-top mt-4">
						<div class="d-flex justify-content-between mt-4">
							<h6>Shipping address</h6>
						</div>
						<span>{{ $order->shipping_name }}</span><br>
						<a href="tel:{{ $order->shipping_phone }}">{{ $order->shipping_phone }}</a><br>
						<a href="mailto:{{ $order->shipping_email }}">{{ $order->shipping_email }}</a><br>
						<span>{{ $order->shipping_address }}</span><br>
						<span>{{ $order->shipping_city }}</span><br>
						<span>{{ $order->shipping_state }}</span><br>
						<span>{{ $order->shipping_zip }}</span><br>
						<span>{{ $order->shipping_country }}</span>
					</div>
					<div class="col-12 border-top mt-4"> 
						<h6 class="mt-4">Return reason</h6>
						<p class="text-danger">{{ $order->reason }}</p>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript" src="{{asset('/assets/vendor/js/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		document.getElementById("status-form").addEventListener("submit", function(e){
			e.preventDefault();
			upadetStatus(this);
		});
	});

	async function upadetStatus(form){
		const formData = new FormData(form);
		try{
			const response = await fetch("{{url('/admin/order-return/status/edit')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				Toast("success", result.message)
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.error(error);
			Toast("error", "Something went wrong, try again later.");
		}
	}
</script>
@endsection