@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">New coupon</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/coupons') }}">Coupons</a></li>
			    <li class="breadcrumb-item active" aria-current="page">New coupon</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="container-fluid p-0">
		<form method="post" action="">
			@csrf
			<div class="row">
				<div class="col-lg-8">
					<div class="card border-0 rounded-4 p-4">
						<div class="row">
							<div class="col-12">
								<label class="form-label">Coupon code</label>
								<div class="input-group mb-3">
								  	<input type="text" class="form-control" name="code" aria-describedby="basic-addon2" max="9">
								  	<span id="generate-coupon-code" class="input-group-text text-info pointer" id="basic-addon2">Generate coupon code</span>
								</div>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Description</label>
								<textarea class="form-control" name="description" rows="3"></textarea>
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Type</label>
								<select class="form-select form-control" name="type">
									<option value="percent">Percentage discount (%)</option>
									<option value="amount">Amount discount</option>
									<option value="free_shipping">Free shipping</option>
								</select>
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Value</label>
								<input class="form-control" type="number" name="value" >
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Per user limit</label>
								<input class="form-control" type="number" name="user_limit" placeholder="0 for unlimited">
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Usage limit</label>
								<input class="form-control" type="number" name="usage_limit" placeholder="0 for unlimited">
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Maximum spend</label>
								<input class="form-control" type="number" name="maximum_spend" placeholder="No maximum">
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Minimum spend</label>
								<input class="form-control" type="number" name="minimum_spend" placeholder="No minimum">
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 mt-4 mt-lg-0">
					<div class="card border-0 rounded-4 p-4">
						<h5>Duration</h5>
						<div class="row">
							<div class="col-12">
								<label class="form-label mt-4">Start date</label>
								<input class="form-control" type="datetime-local" name="start_date">
							</div>
							<div class="col-12">
								<label class="form-label mt-4">End date</label>
								<input class="form-control" type="datetime-local" name="end_date">
							</div>
						</div>
					</div>
					<div class="card border-0 rounded-4 p-4 mt-4">
						<h5>Publish</h5>
						<div class="row">
							<div class="col-12">
								<div class="form-check mt-4">
									 <input class="form-check-input" type="checkbox" value="1" name="status" id="published">
									 <label class="form-check-label" for="published">
									   published
									 </label>
								</div>
							</div>
							<div class="col-12">
								<button class="btn btn-primary btn-sm mt-4">Save</button>
							</div>
						</div>
					</div>
				</div>
			</div>
		</form>
	</div>
</section>
<script type="text/javascript">
	document.addEventListener("DOMContentLoaded", function(event) {
		/*generate random string (0-9 a-z A-Z)*/
		document.getElementById("generate-coupon-code").addEventListener("click", function(){
			const code = Math.random().toString(36).slice(2,10).toUpperCase();
			document.querySelector('input[name="code"]').value = code;
		});
	});
</script>
@endsection