@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">New slider</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/sliders') }}">Sliders</a></li>
			    <li class="breadcrumb-item active" aria-current="page">New slider</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<form action="{{ url('/admin/slider/create') }}" method="post" enctype="multipart/form-data">
		@csrf
		<div class="row">
			<div class="col-lg-9">
				<div class="card border-0 rounded-4 p-4">
					<div class="row">
						<div class="col-12">
							<label class="form-label mt-4">Title<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="title">
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Sub title<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="sub_title">
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Highlight text<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="highlight_text">
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Link<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="link" placeholder="/ptoducts">
						</div>
						<div class="col-12">
							<div class="row">
								<div class="col-12 col-lg-6 col-md-6 ">
									<label class="form-label mt-4">Type</label>
									<select class="form-select form-control" name="type">
										<option value="products">Products</option>
										<option value="category">Category</option>
										<option value="brand">Brand</option>
									</select>
								</div>
								<div class="col-12 col-lg-6 col-md-6 ">
									<label class="form-label mt-4">Type value</label>
									<select class="form-select form-control" name="type_id">
										<option value="0" disabled>Select</option>
										<option value="0" disabled>Categories</option>
										@foreach($categories as $category)
										<option value="{{ $category->id }}">|___{{ $category->name }}</option>
										@endforeach
										<option value="0" disabled>Brands</option>
										@foreach($brands as $brand)
										<option value="{{ $brand->id }}">|___{{ $brand->name }}</option>
										@endforeach
										
									</select>
								</div>
							</div>
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Description</label>
							<textarea class="form-control" rows="3" name="description" id="description"></textarea>
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Button text<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="text" name="button_text">
						</div>
						<div class="col-6">
							<label class="form-label mt-4">Order<span class="text-danger ms-1">*</span></label>
							<input class="form-control" type="number" name="order" placeholder="0">
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
								<option value="published">Published</option>
								<option value="draft">Draft</option>
								<option value="pending">Pending</option>
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