@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Edit order</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/orders') }}">Orders</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Review</li>
			 </ol>
		</div>
	</div>
</section>
<section class="">
	<div class="row">
		<div class="col-lg-8">
			<div class="card border-0 rounded-4 p-4">
				<h5>Order Information {{ $order->code }}</h5>
				<div  class="py-4">
					@switch($order->status)
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
						@case("refunded")
						@case("cancelled")
						<span class="badge badge-warning">{{ $order->status }}</span>
						@break
					@endswitch
				</div>
				<div class="">
					<table class="table table-borderless">
						<thead>
							<tr>
								<th>Name</th>
								<th></th>
								<th>Cost</th>
								<th>Qty</th>
								<th>Total</th>
							</tr>
						</thead>
						<tbody>
							@foreach($order_items as $item)
							<tr>
								<td>
									<img class="rounded-2 shadow-sm" src="{{ url('/assets/img/products').'/'.$item->image }}" width="40" height="40">
								</td>
								<td>
									<a href="{{ url('/admin/product/edit').'/'.$item->product_id }}">{{ $item->name }}</a>
									<p class="m-0">SKU: {{ $item->sku }}</p>
								</td>
								<td>{{ Util::currencySymbol() }}{{ $item->price }}</td>
								<td>{{ $item->quantity }}</td>
								<td>{{ Util::currencySymbol() }}{{ $item->total_price }}</td>
							</tr>
							@endforeach
							<tr>
								@php
									$sub_total = 0.0;
									foreach($order_items as $item){
										$sub_total += $item->total_price;
									}
								@endphp
								<td class="text-end" colspan="4">Items subtotal</td>
								<td>{{ Util::currencySymbol() }}{{ $sub_total }}</td>
							</tr>
							<tr>
								<td class="text-end" colspan="4">Shipping fee</td>
								<td>{{ Util::currencySymbol() }}{{ $order->shipping_amount }}</td>
							</tr>
							<tr>
								<td class="text-end" colspan="4">Order total</td>
								<td>{{ Util::currencySymbol() }}{{ $order->amount }}</td>
							</tr>
						</tbody>
					</table>
				</div>
				<div class="row">
					<div class="col-12 text-end">
						<a class="btn btn-sm btn-primary text-white px-3 me-2" href="{{ url('/admin/order/generate-invoice') }}/{{ $order->id }}?type=print" target="blank">
							<i class="bi-printer text-white me-2"></i>
							Print invoice
						</a>
						<a class="btn btn-sm btn-info text-white px-3" href="{{ url('/admin/order/generate-invoice') }}/{{ $order->id }}" target="blank">
							<i class="bi-download text-white me-2"></i>
							Dowload invoice
						</a>

					</div>
				</div>
				<div class="row">
					<div class="col-12 border-top py-4 mt-4">
						@if($order->is_confirmed)
						<div class="d-flex justify-content-between align-items-center">
							<span>ORDER CONFIRMED</span>
							<span class="badge badge-success ms-2">ok</span>
						</div>
						@else
						<form id="order-confirm-form">
							@csrf
							<input type="hidden" name="confirm_order" value="1">
							<input type="hidden" name="id" value="{{ $order->id }}">
							<div class="d-flex justify-content-between align-items-center">
								<span>CONFIRM ORDER</span>
								<button class="btn btn-sm btn-gradient-primary">Confirm</button>
							</div>
						</form>
						@endif
					</div>
					<div class="col-12 border-top py-4">
						@if($order->payment_status == "pending" || $order->payment_status == "processing")
						<form id="confirm-payment-form" method="post">
							@csrf
							<input type="hidden" name="id" value="{{ $order->id }}">
							<div class="d-flex justify-content-between align-items-center">
								<span>PENDING PAYMENT ({{ $order->payment_method }})</span>
								<button class="btn btn-sm btn-outline-primary">Confirm payment</button>
							</div>
						</form>
						@elseif($order->payment_status == "refunded")
						<div class="d-flex justify-content-between align-items-center">
							<span>PAYMENT REFUNDED</span>
						</div>
						@elseif($order->payment_status == "completed")
						<form id="refund-form" method="post">
							@csrf
							<input type="hidden" name="refund" value="1">
							<input type="hidden" name="id" value="{{ $order->id }}">
							<div class="d-flex justify-content-between align-items-center">
								<span>PAID Ksh <b>{{ $order->amount }}</b> ({{ $order->payment_method }})</span>
								<button class="btn btn-sm btn-outline-primary">Refund</button>
							</div>
						</form>
						@else
						<div class="d-flex justify-content-between align-items-center">
							<span class="text-uppercase">PAYMENT {{ $order->payment_status }}</span>
						</div>
						@endif
					</div>
				</div>
			</div>
			@if($order->downloadable)
			<div class="card border-0 rounded-4 p-4 mt-4">
				<h5>Downloadable product permission</h5>
				<form id="file-access-form" method="post">
					@csrf
					<input type="hidden" name="id" value="{{ $order->id }}">
					<div class="row">
						<div class="col-12 mt-4">
							<div class="form-check">
								<input class="form-check-input" type="radio" name="access" id="revoked" value="revoked" @if($order->download_access == "revoked")
								checked @endif>
								<label class="form-check-label" for="revoked">Revoked access</label>
							</div>
							<div class="form-check">
								<input class="form-check-input" type="radio" name="access" id="granted" value="granted" @if($order->download_access == "granted")
								checked @endif>
								<label class="form-check-label" for="granted">Granted access</label>
							</div>
						</div>
						<div class="col-12">
							<button class="btn btn-sm btn-outline-primary mt-4">Update</button>
						</div>
					</div>
				</form>
			</div>
			@endif
			@if(count($histories))
			<div class="card border-0 rounded-4 p-4 mt-4">
				<h5 class="mb-4">History</h5>
				<div class="row">
					@foreach($histories as $history)
					<div class="col-12 border-top py-3">
						 <div class="d-flex justify-content-between">
						 	<span>{{ $history->description }}</span>
						 	<span>{{ $history->created_at }}</span>
						 </div>
					</div>
					@endforeach
				</div>
			</div>
			@endif
		</div>
		<div class="col-lg-4">
			<div class="card border-0 rounded-4 p-4 mt-4 mt-lg-0">
				<h5>Customer</h5>
				<div class="row">
					<div class="col-12 ">
						<div class="d-flex mt-4">
							<img class="rounded-circle shadow-sm me-4" src="{{url('/assets/img/users').'/'.$order_user->image }}" width="45" height="45">
							<div class="">
								<a href="{{ url('/admin/user/edit').'/'.$order_user->id }}"><h6>{{ $order_user->first_name }} {{ $order_user->last_name }}</h6></a>
								<span class="fw-bold">Email: </span><a href="mailto:{{ $order_user->email }}">{{ $order_user->email }}</a><br>
								<span class="fw-bold">Phone: </span><a href="tel:{{ $order_user->phone }}">{{ $order_user->phone }}</a>
							</div>
						</div>
						
					</div>
					@if($order_address != null)
					<div class="col-12 border-top mt-4">
						<div class="d-flex justify-content-between mt-4">
							<h6>Order Address</h6>
							<a id="edit-shipping-address" href="javascript:"><i class="bi-pencil"></i> </a>
						</div>
						<span>{{ $order_address->name }}</span><br>
						<a href="tel:{{ $order_address->phone }}">{{ $order_address->phone }}</a><br>
						<a href="mailto:{{ $order_address->email }}">{{ $order_address->email }}</a><br>
						<span>{{ $order_address->address }}</span><br>
						<span>{{ $order_address->zip_code }}</span><br>
						<span>{{ $order_address->city }}</span><br>
						<span>{{ $order_address->state }}</span><br>
						<span>{{ $order_address->country }}</span>
					</div>
					@endif
				</div>
				@if($order_address != null)
				<form class="d-none" id="shipping-address-form" method="post">
					@csrf
					<input type="hidden" name="id" value="{{ $order_address->id }}">
					<div class="row">
						<div class="col-12">
							<label class="form-lable mt-4">Name</label>
							<input class="form-control" type="text" name="name" value="{{ $order_address->name }}">
						</div>
						<div class="col-12">
							<label class="form-lable mt-4">Phone</label>
							<input class="form-control" type="tel" name="phone" value="{{ $order_address->phone }}">
						</div>
						<div class="col-12">
							<label class="form-lable mt-4">Email</label>
							<input class="form-control" type="email" name="email" value="{{ $order_address->email }}">
						</div>
						<div class="col-12">
							<label class="form-lable mt-4">Address</label>
							<input class="form-control" type="text" name="address" value="{{ $order_address->address }}">
						</div>
						<div class="col-12">
							<label class="form-lable mt-4">City</label>
							<input class="form-control" type="text" name="city" value="{{ $order_address->city }}">
						</div>
						<div class="col-12">
							<label class="form-lable mt-4">Country</label>
							<select class="form-select form-control" name="country"> 
								@foreach(Util::$countries as $country )
								<option value="{{ $country }}" @if($country == $order_address->country) selected @endif>{{$country}}</option>
								@endforeach
							</select>
						</div>
						<div class="col-12">
							<label class="form-lable mt-4">State/county/Province</label>
							<input class="form-control" type="text" name="state" value="{{ $order_address->state }}">
						</div>
						<div class="col-12">
							<label class="form-lable mt-4">Zip code</label>
							<input class="form-control" type="text" name="zip_code" value="{{ $order_address->zip_code }}">
						</div>
						<div class="col-12">
							<button class="btn btn-sm btn-gradient-primary mt-4">Update</button>
						</div>
					</div>
					@endif
				</form>
			</div>
			<div class="card border-0 rounded-4 p-4 mt-4">
				<h5 class="mb-4">Status</h5>
				<form id="order-status-form" method="post">
					@csrf
					<input type="hidden" name="id[]" value="{{ $order->id }}">
					<div class="row">
						<div class="col-12">
							<select class="form-select form-control" name="status">
								<option value="pending" @if($order->status == "pending") selected @endif>Pending</option>
								<option value="confirmed" @if($order->status == "comfirmed") selected @endif>Confirmed</option>
								<option value="processing" @if($order->status == "processing") selected @endif>Processing</option>
								<option value="completed" @if($order->status == "completed") selected @endif>Completed</option>
								<option value="cancelled" @if($order->status == "cancelled") selected @endif>Cancelled</option>
								<option value="cancelled" @if($order->status == "refunded") selected @endif>Refunded</option>
							</select>
						
						</div>
						<div class="col-12">
							<button class="btn btn-sm btn-primary mt-4 me-4" id="update-status">Update</button>
						</div>
					</div>
				</form>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		if (document.getElementById("edit-shipping-address") != null) {
			document.getElementById("edit-shipping-address").addEventListener("click", function(){
				document.getElementById("shipping-address-form").classList.toggle("d-none");
			});
		}

		if(document.getElementById("shipping-address-form") != null){
			document.getElementById("shipping-address-form").addEventListener("submit", function(e){
				e.preventDefault();
				updateShippingAddress(this);
			});
		}
		
		if (document.getElementById("order-status-form") != null) {
			document.getElementById("order-status-form").addEventListener("submit", function(e){
				e.preventDefault();
				updateStatus(this)
			});
		}

		if (document.getElementById("file-access-form") != null) {
			document.getElementById("file-access-form").addEventListener("submit", function(e){
				e.preventDefault();
				downloadAccess(this);
			});
		}

		if (document.getElementById("confirm-payment-form") != null) {
			document.getElementById("confirm-payment-form").addEventListener("submit", function(e){
				e.preventDefault();
				const form = this;

				confirmAction("Are you sure the customer paid?",
					function(){
						confirmPayment(form);
					},
					function(){},
					"Confirm payment"
				);
			});
		}

		if (document.getElementById("order-confirm-form") != null) {
			document.getElementById("order-confirm-form").addEventListener("submit", function(e){
				e.preventDefault();
				const form = this;
				confirmAction("Are you sure you have confirmed everything?",
					function(){
						confirmOrder(form);
					},
					function(){},
					"Confirm order"
				);
			});
		}
		
		if (document.getElementById("refund-form") != null) {
			document.getElementById("refund-form").addEventListener("submit", function(e){
				e.preventDefault();
				refundOrder(this);
			});
		}
		
	});

	async function confirmPayment(form){

		const formData = new FormData(form);

		try{
			const response = await fetch("{{url('/admin/order/payment/confirm')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				location.reload();
				Toast("success", result.message)
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.error(error);
			Toast("error", "Something went wrong, try again later.");
		}
	}

	async function confirmOrder(form){

		const formData = new FormData(form);

		try{
			const response = await fetch("{{url('/admin/order/confirm')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				location.reload();
				Toast("success", result.message)
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.error(error);
			Toast("error", "Something went wrong, try again later.");
		}
	}

	async function updateShippingAddress(form){

		const formData = new FormData(form);

		try{
			const response = await fetch("{{url('/admin/order/shipping-address/update')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				location.reload();
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.error(error);
			Toast("error", "Something went wrong, try again later.");
		}
	}

	async function updateStatus(form){

		const formData = new FormData(form);

		try{
			const response = await fetch("{{url('/admin/order/status/edit')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				location.reload();
				Toast("success", result.message)
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.error(error);
			Toast("error", "Something went wrong, try again later.");
		}
	}

	async function downloadAccess(form){

		const formData = new FormData(form);

		try{
			const response = await fetch("{{url('/admin/order/download/access')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				location.reload();
				Toast("success", result.message)
			}else{
				console.error(await response.text());
			}
		}catch(error){
			console.error(error);
			Toast("error", "Something went wrong, try again later.");
		}
	}

	async function refundOrder(form){

		const formData = new FormData(form);

		try{
			const response = await fetch("{{url('/admin/order/refund')}}",{
				method: "POST",
				mode: "cors",
				cache: "no-cache",
				credentials: "same-origin",
				body: formData,
			});

			if (response.ok) {
				const result = await response.json();
				location.reload();
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