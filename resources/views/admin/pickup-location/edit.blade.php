@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Edit pickup location</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/pickup-locations') }}">Pickup locations</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Edit pickup location</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="container-fluid p-0">
		<form method="post" action="{{ url('/admin/pickup-location/edit').'/'.$pickupLocation->id }}">
			@csrf
			<div class="row">
				<div class="col-lg-9">
					<div class="card border-0 rounded-4 p-4">
						<div class="row">
							<div class="col-12">
								<label class="form-label">Name<span class="text-danger"> *</span></label>
								<input type="text" class="form-control" name="name" placeholder="Building, floor, city" value="{{ $pickupLocation->name }}" required>
							</div>
							<div class="col-6">
								<label class="form-label mt-4">City<span class="text-danger"> *</span></label>
								<select class="form-select form-control" name="city">
									@if(count($cities))
									@foreach($cities as $city)
										@if($city->id == $pickupLocation->city_id)
										<option value="{{ $city->id }}" selected>{{ $city->name }}</option>
										@else
										<option value="{{ $city->id }}">{{ $city->name }}</option>
										@endif
									@endforeach
									@endif
								</select>
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Zone<span class="text-danger"> *</span></label>
								<select class="form-select form-control" name="zone">
									@if(count($zones))
									@foreach($zones as $zone)
										@if($zone->id == $pickupLocation->zone_id)
										<option value="{{ $zone->id }}" selected>{{ $zone->name }}</option>
										@else
										<option value="{{ $zone->id }}">{{ $zone->name }}</option>
										@endif
									@endforeach
									@endif
								</select>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Gps location<span class="text-danger"> *</span></label>
								<input type="text" class="form-control" name="gps_link" placeholder="https://map.google.com/3735gr3rg8r38gg" value="{{ $pickupLocation->gps_link }}" required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Description<span class="text-danger"> *</span></label>
								<textarea class="form-control" name="description" rows="3">{{ $pickupLocation->description }}</textarea>
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
									<option value="pending" @if($pickupLocation->status == "pending") selected @endif>Pending</option>
									<option value="draft" @if($pickupLocation->status == "draft") selected @endif>Draft</option>
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