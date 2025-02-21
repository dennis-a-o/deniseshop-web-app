@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">New flash sale</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/flash-sales') }}">Flash sales</a></li>
			    <li class="breadcrumb-item active" aria-current="page">New flash sale</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section>
	<div class="container-fluid p-0">
		<form method="post" action="" enctype="multipart/form-data">
			@csrf
			<div class="row">
				<div class="col-lg-8">
					<div class="card border-0 rounded-4 p-4">
						<div class="row">
							<div class="col-12">
								<label class="form-label">Name</label>
								<input type="text" class="form-control" name="name">
							</div>
							<div class="col-12">
								<label class="form-label mt-4">Description</label>
								<textarea class="form-control" name="description" rows="2"></textarea>
							</div>
						</div>
					</div>
					<div class="card border-0 rounded-4 p-4 mt-4">
						<h5>Products</h5>
						<div class="row">
							<div class="col-12 mt-4">
								<input class="form-control" type="text" name="search-products" placeholder="Search products">
							</div>
							<div class="col-12">
								<div id="product-search-list" style="display: none;">
									<div class="card border-0 rounded-4 shadow">
										<ul class="list-group list-group-flush">	
										</ul>
										<div class="card-footer">
											<div class="d-flex justify-content-between pt-2">
												<div>
													<button id="add-selected" type="button" class="btn btn-gradient-primary btn-sm">Add selected</button>
													<button id="cancel-search" type="button" class="btn btn-outline-primary btn-sm">cancel</button>
												</div>
												<ul class="pagination float-end">
													<li class="page-item">
														<a id="previous-search-page" class="page-link shadow-none" href="Javascript:" aria-label="Previous">
															<span aria-hidden="true">&laquo;</span>
														</a>
													</li>
													<li class="page-item">
														<a id="next-search-page" class="page-link shadow-none" href="Javascript:" aria-label="Next">
															<span aria-hidden="true">&raquo;</span>
														</a>
													</li>
												</ul>
											</div>
										</div>
									</div>
								</div>
							</div>
							<div class="col-12" >
								<h6 class="mt-4">Selected products</h6>
								<div id="product-list">
									<ul class="list-group list-group-flush"></ul>
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="col-lg-4 mt-4 mt-lg-0">
					<div class="card border-0 rounded-4 p-4">
						<h5>Publish</h5>
						<div class="row">
							<div class="col-12">
								<label class="form-label">Status</label>
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
					<div class="card border-0 rounded-4 p-4 mt-4">
						<h5>Duration</h5>
						<div class="row">
							<div class="col-12">
								<label class="form-label mt-4">End date</label>
								<input class="form-control" type="datetime-local" name="end_date">
							</div>
						</div>
					</div>
					<div class="card border-0 rounded-4 p-4 mt-4">
						<h5>Image</h5>
						<label class="pointer image-input-label">
							<img height="150" class="w-100 shadow-sm rounded-3 mt-4" src="" id="image-preview">
							<input type="file" name="image" class="d-none" id="image-input">
						</label>
					</div>
				</div>
			</div>
		</form>
	</div>
</section>
<script type="text/javascript" src="{{ asset('/assets/admin/js/flashsale.js') }}"></script>
@endsection