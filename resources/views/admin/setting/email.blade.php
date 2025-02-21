@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Email setting</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Email setting</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<form action="{{ url('/admin/setting/email') }}" method="post">
		<div class="row">
			<div class="col-lg-8">
				<div class="card border-0 rounded-4 p-4">
					<div class="row">
						@csrf
						<div class="col-12">
							<label class="form-label">Sender name <i class="text-danger">*</i></label>
							<input class="form-control" type="text" name="email_sender_name" value="{{ $setting['email_sender_name'] ?? '' }}">
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Contact email</label>
							<input class="form-control" type="text" name="contact_email" value="{{ $setting['contact_email'] ?? '' }}">
						</div>
						<div class="col-12">
							<label class="form-label mt-4">No reply email</label>
							<input class="form-control" type="text" name="no_reply_email" value="{{ $setting['no_reply_email'] ?? '' }}">
						</div>
						
						<div class="col-12">
							<button class="btn btn-primary btn-sm mt-4">Save settings</button>
						</div>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>
@endsection