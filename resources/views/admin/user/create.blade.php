@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">New user</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/users') }}">Users</a></li>
			    <li class="breadcrumb-item active" aria-current="page">New user</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<form action="{{ url('/admin/user/create') }}" method="post" enctype="multipart/form-data">
		@csrf
		<div class="row">
			<div class="col-lg-9">
				<div class="card border-0 rounded-4 p-4">
					<div class="row">
						<div class="col-6">
							<label class="form-label mt-4">First name<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="first_name" required>
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Last name<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="last_name" required>
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Email<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="email" required>
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Password<span class="text-danger ms-1">*</span></label>
							<div class="input-group">
								<input type="password" class="form-control border-end-0" name="password" required>
								<span class="input-group-text bg-white border-start-0"><i id="toggle-password" class="bi-eye-slash pointer"></i> </span>
							</div>
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Role<span class="text-danger ms-1">*</span></label>
							<select class="form-select form-control" name="role">
								<option value="User">User</option>
								<option value="Manager">Manager</option>
								<option value="Admin">Admin</option>
							</select>
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Verification<span class="text-danger ms-1">*</span></label>
							<select class="form-select form-control" name="verification">
								<option value="verify">Verified</option>
								<option value="unverify">Unverified</option>
							</select>
						</div>
						<div class="col-12">
							<div class="form-check mt-4">
								<input class="form-check-input" type="checkbox" role="switch" id="subscribe" name="subscribe" value="1">
								<label class="form-check-label ms-1" for="subscribe">Subscribe user</label>
							</div>
						</div>
						<div class="col-12">
							<div class="form-check mt-4">
								<input class="form-check-input" type="checkbox" role="switch" id="notify" name="notify" value="1">
								<label class="form-check-label ms-1" for="notify">Send user notification</label>
							</div>
						</div>

					</div>
				</div>
			</div>
			<div class="col-lg-3">
				<div class="card border-0 rounded-4 p-4 mt-lg-0 mt-4">
					<h5>Publish</h5>
					<div class="row">
						<div class="col-12">
							<label class="form-label mt-4">Status</label>
							<select class="form-select form-control" name="status">
								<option value="activated">Activated</option>
								<option value="locked">Locked</option>
							</select>
						</div>
						<div class="col-12">
							<button class="btn btn-primary btn-sm mt-4"><i class="bi-ok text-white"></i> Save</button>
						</div>
					</div>
				</div>

				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Image</h5>
					<label class="pointer image-input-label" for="image-input">
						<img height="100" class="w-100 shadow-sm rounded-3 mt-4" src="" id="image-preview">
						<input type="file" name="image" class="d-none" id="image-input">
					</label>
				</div>
			</div>
		</div>
	</form>
</section>
@endsection