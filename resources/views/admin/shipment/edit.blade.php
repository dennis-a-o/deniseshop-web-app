@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Edit Shipment</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/shipments') }}">Shipments</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Edit shipments</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<div class="fluid-container">
		<div class="row">
			<div class="col-lg 8">
				<div class="card border-0 rounded-4 p-4">
					<table class="table table-bordered m-0">
						<tbody>
							@foreach($orderItems as $orderItem)
							<tr>
								<td>
									<div class="d-flex">
										<img class="rounded-2 shadow-sm me-4"  src="{{url('/assets/img/products').'/'.$orderItem->image }}" width="40" height="40">
										<div>
											<a href="{{ url('/admin/product/edit').'/'.$orderItem->product_id }}">{{ $orderItem->name }}</a><br>
											<span>(Color: {{ $orderItem->color }}, Size: {{ $orderItem->size }})</span><br>
											<span class="text-uppercase">SKU: {{ $orderItem->sku }}</span>
										</div>
									</div>	
								</td>
								<td>
									<span>{{ $orderItem->quantity }} x</span>
									<span class="text-info">{{ Util::currencySymbol().$orderItem->price }}</span>
								</td>
								<td><span class="opacity-75">{{ Util::currencySymbol().$orderItem->total_price }}</span></td>
							</tr>
							@endforeach
						</tbody>
					</table>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Shipping information</h5>
					<form action="" method="post">
						@csrf
						<input type="hidden" name="id" value="{{ $shipment->id }}">
						<div class="row">
							<div class="col-12">
								<label class="form-label mt-4">Shipping company name</label>
								<input class="form-control" type="text" name="shipping_company_name" placeholder="e.g G4S" value="{{ $shipment->shipping_company_name }}">
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Tracking id</label>
								<input class="form-control" type="text" name="tracking_id" placeholder="e.g GXXX2024" value="{{ $shipment->tracking_id }}">
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Tracking url</label>
								<input class="form-control" type="url" name="tracking_url" placeholder="e.g http://ww.g4s.com" value="{{ $shipment->tracking_url }}">
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Estimate date shipped</label>
								<input class="form-control" type="datetime-local" name="estimate_date_shipped" placeholder="e.g G4S" value="{{ $shipment->estimate_date_shipped }}">
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Note</label>
								<textarea class="form-control" name="note" placeholder="Note ..." rows="2">{{ $shipment->note }}</textarea>
							</div>
							<div class="col-12">
								<button class="btn btn-sm btn-primary mt-4">Save</button>
							</div>
						</div>
					</form>
				</div>
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
			<div class="col-lg-4 mt-4 mt-lg-0">
				<div class="card border-0 rounded-4 p-4">
					<h5>Shipment information</h5>
					<div class="card-body p-0 mt-4">
						<div class="d-flex justify-content-between">
							<span>Order number</span>
							<a href="{{ url('/admin/order/edit').'/'.$order->id }}">{{ $order->code }}</a>
						</div>
						<div class="d-flex justify-content-between mt-3">
							<span>Shipping method</span>
							<span>{{ $order->shipping }}</span>
						</div>
						<div class="d-flex justify-content-between mt-3">
							<span>Shipping fee</span>
							<span>{{ Util::currencySymbol().$shipment->price }}</span>
						</div>
						<div class="d-flex justify-content-between mt-3">
							<span>Shipping status</span>
							@switch($shipment->status)
								@case("cancelled")
								@case("pending")
									<span class="badge badge-warning">{{ $shipment->status }}</span>
									@break
								@case("completed")	
									<span class="badge badge-success">{{ $shipment->status }}</span>
									@break
								@default
									<span class="badge badge-info">{{ $shipment->status }}</span>
							@endswitch
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Customer information</h5>
					<div class="card-body p-0 mt-4">
						<span>{{ $order->customer_name }}</span><br>
						<a href="tel:{{ $order->customer_phone }}">{{ $order->customer_phone}}</a><br>
						<a href="mailto:{{ $order->customer_email }}">{{ $order->customer_email }}</a><br>
						<span>{{ $order->customer_address }}</span><br>
						<span>{{ $order->customer_zip }}</span><br>
						<span>{{ $order->customer_city }}</span><br>
						<span>{{ $order->customer_state }}</span>
						<span>{{ $order->customer_country }}</span>
					</div>
				</div>
				<div class="mt-4 p-4">
					<div class="d-inline-flex">
						<select id="status-select" class="form-select form-control bg-info text-white" name="status">
							<option>Update shipping status</option>
							<option value="not_approved">Not approved</option>
							<option value="approved">Approved</option>
							<option value="pending">Pending</option>
							<option value="arrange_shipment">Arrange shipment</option>
							<option value="ready_to_be_shipped_out">Ready to be shipped out</option>
							<option value="picking">Picking</option>
							<option value="delay_picking">Delay picking</option>
							<option value="picked">picked</option>
							<option value="not_picked">Not picked</option>
							<option value="delivering">Delivering</option>
							<option value="delivered">Delivered</option>
							<option value="not_delivered">Not delivered</option>
							<option value="cancelled">Cancelled</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {

		document.getElementById("status-select").addEventListener("change", function(){
			var status = this.value;
			confirmAction("Do you really want to confirm "+status+" for this shipment?",
				function(){
					shipmentStatus(status);
				},
				function(){},
				"Confirm "+status+"?"
			);
		});

	});

	async function shipmentStatus(status){

		const formData = new FormData();
		
		formData.append("id[]", document.querySelector('input[name="id"]').value);
		formData.append("status", status);
		formData.append("_token",  document.querySelector('meta[name="csrf-token"]').content);

		try{
			const response = await fetch("{{url('/admin/shipment/status/edit')}}",{
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