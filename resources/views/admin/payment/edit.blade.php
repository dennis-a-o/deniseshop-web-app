@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Transaction detail</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/payment/transactions') }}">Transactions</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Transaction detail</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="row">
		 <div class="col-lg-8 col-md-9">
		 	<div class="card border-0 rounded-4 p-4">
		 		<p>Created at: <strong>{{ $payment->created_at }}</strong></p>
		 		<p>Payment channel: <strong>{{ $payment->payment_channel }}</strong> </p>
		 		<p>Transaction id: <strong>{{ $payment->transaction_id }}</strong> </p>
		 		<p>Total: <strong>{{ $payment->currency.' '.$payment->amount }}</strong> </p>
		 		<p>Status: <strong>{{ $payment->status }}</strong> </p>
		 		<p>Payer name: <strong>{{ $payment->user->first_name.' '.$payment->user->last_name }}</strong> </p>
		 		<p>Email: <strong>{{ $payment->user->email }}</strong> </p>
		 		<div class="d-flex mt-4">
		 			<a class="btn btn-success btn-sm me-4" href="{{ url('/admin/order/generate-invoice') }}/{{ $payment->order_id }}?type=print" target="blank">Print invoice</a>
		 			<a class="btn btn-outline-success btn-sm" href="{{ url('/admin/order/generate-invoice') }}/{{ $payment->order_id }}" target="blank">Download invoice</a>
		 		</div>
		 	</div>
		 </div>
		 <div class="col-lg-4 col-md-3 mt-4 mt-lg-0">
		 	<form action="" method="post">
		 		@csrf
		 		<div class="card border-0 rounded-4 p-4">
		 			<h5>Publish</h5>
		 			<div class="row">
		 				<div class="col-12">
		 					<label class="form-label mt-4">Status</label>
		 					<select class="form-select form-control" name="status">
		 						<option value="pending" @if($payment->status == "pending") selected @endif >Pending</option>
								<option value="completed" @if($payment->status == "completed") selected @endif>Completed</option>
								<option value="refunded" @if($payment->status == "refunded") selected @endif>Refunded</option>
								<option value="fraud" @if($payment->status == "fraud") selected @endif>Fraud</option>
								<option value="failed" @if($payment->status == "failed") selected @endif>Failed</option>
		 					</select>
		 				</div>
		 				<div class="col-12">
		 					<button class="btn btn-primary btn-sm mt-4 float-end" >Update</button>
		 				</div>
		 			</div>
		 		</div>
		 	</form>
		 </div>
	</div>
</section>
@endsection