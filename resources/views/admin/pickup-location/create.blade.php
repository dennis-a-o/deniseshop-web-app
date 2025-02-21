@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">New pickup location</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/pickup-locations') }}">Pickup locations</a></li>
			    <li class="breadcrumb-item active" aria-current="page">New pickup location</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="container-fluid p-0">
		<form method="post" action="{{ url('/admin/pickup-location/create') }}">
			@csrf
			<div class="row">
				<div class="col-lg-9">
					<div class="card border-0 rounded-4 p-4">
						<div class="row">
							<div class="col-12">
								<label class="form-label">Name<span class="text-danger"> *</span></label>
								<input type="text" class="form-control" name="name" placeholder="Building, floor, city" required>
							</div>
							<div class="col-6">
								<label class="form-label mt-4">City<span class="text-danger"> *</span></label>
								<select class="form-select form-control" name="city">
									@if(count($cities))
									@foreach($cities as $city)
									<option value="{{ $city->id }}">{{ $city->name }}</option>
									@endforeach
									@endif
								</select>
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Zone<span class="text-danger"> *</span></label>
								<select class="form-select form-control" name="zone">
									@if(count($zones))
									@foreach($zones as $zone)
									<option value="{{ $zone->id }}">{{ $zone->name }}</option>
									@endforeach
									@endif
								</select>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Gps location<span class="text-danger"> *</span></label>
								<input type="text" class="form-control" name="gps_link" placeholder="https://map.google.com/3735gr3rg8r38gg" required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Description<span class="text-danger"> *</span></label>
								<textarea class="form-control" name="description" rows="3"></textarea>
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