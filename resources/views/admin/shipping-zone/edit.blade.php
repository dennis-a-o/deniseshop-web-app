@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Edit shipping zone</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/shipping-zones') }}">Shipping zones</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Edit shipping zone</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="container-fluid p-0">
		<form method="post" action="{{ url('/admin/shipping-zone/edit').'/'.$zone->id }}">
			@csrf
			<div class="row">
				<div class="col-lg-9">
					<div class="card border-0 rounded-4 p-4">
						<div class="row">
							<div class="col-12">
								<label class="form-label">Name<span class="text-danger"> *</span></label>
								<input type="text" class="form-control" name="name" value="{{ $zone->name }}"  required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Rate (charge in %)<span class="text-danger"> *</span></label>
								<input type="number" class="form-control" name="rate" min="0" max="100" value="{{ $zone->rate }}"  required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Description<span class="text-danger"> *</span></label>
								<textarea class="form-control" name="description" rows="3">{{ $zone->description }}</textarea>
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
									<option value="pending" @if($zone->status == "pending") selected @endif>Pending</option>
									<option value="draft" @if($zone->status == "draft") selected @endif>Draft</option>
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