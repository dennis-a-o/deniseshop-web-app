@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Payment methods</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Payment methods</li>
			 </ol>
		</div>
	</div>
</section>
<section class="">
	<div class="fluid-container p-0">
		<div class="row">
			<div class="col-9">
				<div class="card border-0 rounded-4">
					<div class="d-flex align-items-center px-4 py-2">
						<img class="me-4" src="{{ url('/assets/img/payment-channel') }}/paypal.png " width="" height="27">
						<div>
							<a href="https://paypal.com">PayPal</a>
							<p>Customers pays directly via PayPal.</p>
						</div>
					</div>
					<div class="d-flex justify-content-between align-items-center border-top px-4 py-2">
						<span>Use:</span>
						<button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#paypalCollape" aria-expanded="false" aria-controls="paypalCollape">Edit</button>
					</div>
					<div class="collapse border-top px-4 py-2" id="paypalCollape">
						<div class="row">
							<div class="col-lg-6">
								<div>
									<label>Configuration instructions for PayPal</label>
									<p>To use PayPal, you need:</p>
									<ul>
										<li style="list-style-type: decimal;"><a href="https://www.paypal.com/vn/merchantsignup/applicationChecklist?signupType=CREATE_NEW_ACCOUNT&productIntentId=email_payments">Register with PayPal</a> </li>
										<li style="list-style-type: decimal;"><p>After registration at PayPal, you will have Client ID, Client Secret</p></li>
										<li style="list-style-type: decimal;"><p>Enter Client ID, Secret into the box in right hand</p></li>
									</ul>
								</div>
							</div>
							<div class="col-lg-6" >
								<form  id="paypal-form" method="post"  action="">
									@csrf
									<div class="row">
										<div class="col-12">
											<label class="form-label">Name</label>
											<input class="form-control" type="hidden" name="id" value="{{ $paypal->id ?? 0 }}">
											<input class="form-control" type="text" name="name" value="{{ $paypal->name ?? '' }}" placeholder="e.g Paypal">
										</div>
										<div class="col-12">
											<label class="form-label mt-4">Description</label>
											<textarea class="form-control" type="text" name="description">{{ $paypal->description ?? '' }}</textarea>
										</div>

										@php 
											$credential = json_decode($paypal->credential ?? null);
										@endphp
										<div class="col-12">
											<label class="form-label mt-4">Client ID</label>
											<input class="form-control" type="text" name="client_id" value="{{ $credential->client_id ?? '' }}">
										</div>
										<div class="col-12">
											<label class="form-label mt-4">Client Secret</label>
											<div class="input-group">
												<input type="password" class="form-control border-end-0" name="client_secret" value="{{ $credential->client_secret ?? '' }}" required>
												<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
											</div>
										</div>
										<div class="col-12">
											<div class="form-check mt-4">
												<input class="form-check-input" type="checkbox" name="sandbox" value="1" id="sandbox" @if($paypal->is_sandbox ?? 0) checked @endif>
												<label class="form-check-label" for="sandbox">Sandbox mode</label>
											</div>
										</div>
										<div class="col-12">
											<label class="form-label mt-4">Status</label>
											<select class="form-select form-control" name="status">
												<option value="active" >Active</option>
												<option value="inactive" @if(($paypal->status ?? '') == "inactive") selected @endif>Inactive</option>
											</select>
										</div>
										<div class="col-12">
											<button class="btn btn-primary btn-sm mt-4">Update</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 mt-4">
					<div class="d-flex align-items-center px-4 py-2">
						<img class="me-4" src="{{ url('/assets/img/payment-channel') }}/cod.png " width="" height="27">
						<div>
							<a href="javascript:">Cash On Delivery</a>
							<p>Customers pays directly cash to postman or pickup agent.</p>
						</div>
					</div>
					<div class="d-flex justify-content-between align-items-center border-top px-4 py-2">
						<span>Use:</span>
						<button class="btn btn-outline-secondary btn-sm" data-bs-toggle="collapse" data-bs-target="#codCollape" aria-expanded="false" aria-controls="codCollape">Edit</button>
					</div>
					<div class="collapse border-top px-4 py-2" id="codCollape">
						<div class="row">
							<div class="col-12" >
								<form id="cod-form" method="post" action="">
									@csrf
									<div class="row">
										<div class="col-12">
											<label class="form-label">Name</label>
											<input class="form-control" type="hidden" name="id" value="{{ $cod->id ?? '' }}">
											<input class="form-control" type="text" name="name" value="{{ $cod->name ?? '' }}" placeholder="e.g cod">
										</div>
										<div class="col-12">
											<label class="form-label mt-4">Description</label>
											<textarea class="form-control" type="text" name="description">{{ $cod->description ?? '' }}</textarea>
										</div>
										<div class="col-12">
											<label class="form-label mt-4">Status</label>
											<select class="form-select form-control" name="status">
												<option value="active">Active</option>
												<option value="inactive" @if(($cod->status ?? '')  == "inactive") selected @endif>Inactive</option>
											</select>
										</div>
										<div class="col-12">
											<button class="btn btn-primary btn-sm mt-4">Update</button>
										</div>
									</div>
								</form>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</section>
<script type="text/javascript" src="{{asset('/assets/vendor/js/jquery.dataTables.min.js')}}"></script>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		var paypalForm = document.getElementById("paypal-form");
		var codForm = document.getElementById("cod-form");

		paypalForm.addEventListener('submit', (e) => {
			e.preventDefault();
			submitPaypalForm(paypalForm);
		});

		codForm.addEventListener('submit', (e) => {
			e.preventDefault();
			submitCodForm(codForm);
		});

		async function submitPaypalForm(form){

			var formData = new FormData(form);

			try{
				const response = await fetch("{{url('/admin/payment/method/paypal')}}",{
					method: "POST",
					mode: "cors",
					cache: "no-cache",
					credentials: "same-origin",
					body: formData,
				});

				if (response.ok) {
					const result = await response.json();
					if (result.error) {
						Toast("error", result.message);
					}else{
						Toast("success", result.message);
					}
				}else{
					console.error(await response.text());
				}
			}catch(error){
				console.error(error);
				Toast("error", "Something went wrong, try again later.");
			}
		}

		async function submitCodForm(form){
			
			var formData = new FormData(form);

			try{
				const response = await fetch("{{url('/admin/payment/method/cod')}}",{
					method: "POST",
					mode: "cors",
					cache: "no-cache",
					credentials: "same-origin",
					body: formData,
				});

				if (response.ok) {
					const result = await response.json();
					if (result.error) {
						Toast("error", result.message);
					}else{
						Toast("success", result.message);
					}
				}else{
					console.error(await response.text());
				}
			}catch(error){
				console.error(error);
				Toast("error", "Something went wrong, try again later.");
			}
		}
	});
</script>
@endsection