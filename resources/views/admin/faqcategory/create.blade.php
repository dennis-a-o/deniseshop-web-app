@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">Edit Faq category</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/faq-categories') }}">Faq categories</a></li>
			    <li class="breadcrumb-item active" aria-current="page">Edit faq category</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<div class="fluid-container p-0">
		<div class="row">
			<div class="col-lg-8">
				<div class="card border-0 rounded-4 p-4">
					<form id="faq-category-form" action="{{ url('/admin/faq-category/edit').'/'.$category->id }}" method="post">
						@csrf
						<div class="row">
							<div class="col-12">
								<label class="form-label mt-4">Name</label>
								<input class="form-control"  type="text" name="name" value="{{ $category->name }}" required>
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Description</label>
								<textarea class="form-control" rows="5" name="description">{{ $category->description }} </textarea>
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