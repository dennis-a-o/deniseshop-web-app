@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">New city</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/cities') }}">Cities</a></li>
			    <li class="breadcrumb-item active" aria-current="page">New city</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="container-fluid p-0">
		<form method="post" action="{{ url('/admin/city/create') }}">
			@csrf
			<div class="row">
				<div class="col-lg-9">
					<div class="card border-0 rounded-4 p-4">
						<div class="row">
							<div class="col-12">
								<label class="form-label">Name<span class="text-danger"> *</span></label>
								<input type="text" class="form-control" name="name" required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Country<span class="text-danger"> *</span></label>
								<select class="form-select form-control" name="country">
									@if(count($countries))
									@foreach($countries as $country)
									<option value="{{ $country->id }}">{{ $country->name }}</option>
									@endforeach
									@endif
								</select>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">State<span class="text-danger"> *</span></label>
								<select class="form-select form-control" name="state">
									@if(count($states))
									@foreach($states as $state)
									<option value="{{ $state->id }}">{{ $state->name }}</option>
									@endforeach
									@endif
								</select>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-3 mt-4 mt-lg-0">
					<div class="card border-0 rounded-4 p-4">
						<h5>Publish</h5>
						<div class="row">
							<div class="col-12">
								<label class="form-label mt-4">Status<span class="text-danger"> *</span></label>
								<select class="form-select form-control" name="status">
									<option value="published">Published</option>
									<option value="pending">Pending</option>
									<option value="draft">Draft</option>
								</select>
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
@endsection