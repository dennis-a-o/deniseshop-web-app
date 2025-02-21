@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Edit category</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/product-categories') }}">Product categories</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Edit category</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="fluid-container p-0">
		<div class="row">
			<div class="col-lg-8">
				<div class="card border-0 rounded-4 p-4">
					<form id="categoryForm" action="{{ url('/admin/product-category/edit').'/'.$category->id }}" method="post" enctype="multipart/form-data">
						@csrf
						<div class="row">
							<div class="col-12">
								<label class="form-label mt-4">Name</label>
								<input class="form-control"  type="text" name="name" value="{{ $category->name }}" required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Parent</label>
								<select class="form-select form-control" name="parent">
									<option style="padding-left: 20px ;" value="0">None</option>
									@if(count($categories))
									@foreach($categories as $cat)
									<option  value="{{ $cat->id }}" @if($category->parent_id == $cat->id)selected @endif>{{ $cat->name }}</option>
										@foreach($cat->categories as $sub_category)
										<option  value="{{ $sub_category->id }}" @if($category->parent_id == $sub_category->id)selected @endif>|__{{ $sub_category->name }}</option>
										@endforeach
									@endforeach
									@endif
								</select>
							</div>
							
							<div class="col-12">
								<label class="form-label mt-4">Description</label>
								<textarea class="form-control" rows="5" name="description">{{ $category->description }} </textarea>
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Image</label><br>
								<label class="pointer image-input-label">
									<img class="rounded-4 shadow-sm" src="{{ asset('/assets/img/categories').'/'.$category->image }}" width="75" height="75">
									<input class="form-control d-none" type="file" name="image" id="image-input">
								</label>
								
							</div>
							<div class="col-6">
								<label class="form-label mt-4">Icon</label><br>
								<label class="pointer image-input-label">
									<img class="rounded-4 shadow-sm" src="{{ asset('/assets/img/categories').'/'.$category->icon }}" width="75" height="75">
									<input class="form-control d-none" type="file" name="icon" id="image-input">
								</label>
							</div>
							<div class="col-12">
								<button class="btn btn-sm btn-primary mt-4">Update</button>
							</div>
						</div>
					</form>
				</div>
			</div>
		</div>
	</div>
</section>


@endsection