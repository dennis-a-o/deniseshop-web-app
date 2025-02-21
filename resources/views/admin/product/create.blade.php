@extends('layouts.admin')
@section('content')
<section class="py-4">
	<div class="row align-items-center justify-content-center">
		<div class="col-6">
			<h4 class="fw-bolder m-0">New product</h4>
		</div>
		<div class="col-6">
			<ol class="breadcrumb justify-content-end align-items-center m-0">
				<li class="breadcrumb-item"><a  href="{{ url('/admin/dashboard') }}">Dashboard</a></li>
				<li class="breadcrumb-item"><a  href="{{ url('/admin/products') }}">Products</a></li>
			    <li class="breadcrumb-item active" aria-current="page">New product</li>
			 </ol>
		</div>
	</div>
</section>
@include('includes.form-error')
@include('includes.form-success')
<section class="">
	<form action="{{ url('/admin/product/create') }}" method="post" enctype="multipart/form-data">
		@csrf
		<div class="row">
			<div class="col-lg-9">
				<div class="card border-0 rounded-4 p-4">
					<h5>Product information</h5>
					<div class="row">
						<div class="col-12">
							<label class="form-label mt-4">Name</label>
							<input class="form-control" type="text" name="name" placeholder="e.g Iphone 19 pro">
						</div>
						<div class="col-12">
							<label class="form-label mt-4">Description</label>
							<textarea class="form-control" rows="6" name="description" id="description"></textarea>
						</div>

						<div class="col-12">
							<label class="form-label mt-4">Description summary(optional)</label>
							<textarea class="form-control" rows="4" name="description_summary"></textarea>
						</div>
					</div>
				</div>
			
				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5 class="mb-5">Product data</h5>
					<nav>
						<div class="nav nav-tabs" id="nav-tab" role="tablist">
							<button class="nav-link active" id="nav-general-tab" data-bs-toggle="tab" data-bs-target="#nav-general" type="button" role="tab" aria-controls="nav-pricing" aria-selected="true">General</button>
							<button class="nav-link" id="nav-inventory-tab" data-bs-toggle="tab" data-bs-target="#nav-inventory" type="button" role="tab" aria-controls="nav-inventory" aria-selected="true">Inventory</button>
							<button class="nav-link" id="nav-shipping-tab" data-bs-toggle="tab" data-bs-target="#nav-shipping" type="button" role="tab" aria-controls="nav-profile" aria-selected="false">Shipping</button>
							<button class="nav-link" id="nav-attribute-tab" data-bs-toggle="tab" data-bs-target="#nav-attribute" type="button" role="tab" aria-controls="nav-attribute" aria-selected="false">Attributes</button>
							<button class="nav-link" id="nav-option-tab" data-bs-toggle="tab" data-bs-target="#nav-option" type="button" role="tab" aria-controls="nav-option" aria-selected="false" >Options</button>
						</div>
					</nav>
					<div class="tab-content" id="nav-tabContent">
						<div class="tab-pane fade show active pt-5" id="nav-general" role="tabpanel" aria-labelledby="nav-general-tab" tabindex="0">
							<div class="row">
								<div class="col-12">
									<label class="form-label">Type</label>
									<select class="form-select form-control" name="type" id="product-type">
										<option value="internal">Simple/Internal</option>
										<option value="external">External/Affliate</option>
									</select>
								</div>
								<div class="col-12" id="affliate-input" style="display:none;">
									<label class="form-label mt-4">Url</label>
									<input class="form-control" type="text" name="url" placeholder="https://">
									<label class="form-label mt-4">Button text</label>
									<input class="form-control" type="text" name="button_text" placeholder="Buy product">
								</div>
								<div class="col-6">
									<label class="form-label mt-4">Price regular (Ksh)</label>
									<input class="form-control" type="number" name="price">
								</div>
								<div class="col-6">
									<label class="form-label mt-4">Price sale (Ksh)</label>
									<input class="form-control" type="number" name="sale_price">
								</div>
								<div class="col-6">
									<label class="form-label mt-4">Quantity</label>
									<input class="form-control" type="number" name="quantity">
								</div>
								<div class="col-6">
									<label class="form-label mt-4">Price sale dates</label>
									<input class="form-control" type="datetime-local" name="start_date">
									<input class="form-control mt-4" type="datetime-local" name="end_date">
								</div>
								<div class="col-12">
									<div class="form-check mt-4">
										<input class="form-check-input" type="checkbox" name="downloadable" value="1" id="flexCheckDefault" data-bs-toggle="collapse" data-bs-target="#collapseDownload">
										<label class="form-check-label" for="flexCheckDefault">
											Downloadable?
										</label>
									</div>
								</div>
								<div class="col-12 collapse" id="collapseDownload">
									<label class="form-label mt-4">Download file</label>
									<input class="form-control" type="file" name="download_file">
									<label class="form-label mt-4">Download limit</label>
									<input class="form-control" type="number" name="download_limit">
									<label class="form-label mt-4">Download expiry (number of days)</label>
									<input class="form-control" type="number" name="download_expiry">
								</div>
							</div>
						</div>
						<div class="tab-pane fade pt-5" id="nav-inventory" role="tabpanel" aria-labelledby="nav-inventory-tab" tabindex="0">
							<div class="row">
								<div class="col-12">
									<label class="form-label">SKU</label>
									<input class="form-control" type="text" name="sku">
								</div>
								<div class="col-12">
									<label class="form-label mt-4">Stock status</label>
									<select class="form-select form-control" name="stock_status">
										<option value="in_stock">In stock</option>
										<option value="oot_stock">Out of stock</option>
									</select>
								</div>
							</div>
						</div>
						<div class="tab-pane fade pt-5" id="nav-shipping" role="tabpanel" aria-labelledby="nav-shipping-tab" tabindex="0">
							<div class="row">
								<div class="col-12">
									<label class="form-label">Weight (Kg)</label>
									<input class="form-control" type="number" name="Weight">
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<label class="form-label mt-4">Dimensions (cm)</label>
								</div>
								<div class="col-4">
									<input class="form-control" type="number" name="length" placeholder="Length">
								</div>
								<div class="col-4">
									<input class="form-control" type="number" name="width" placeholder="Width">
								</div>
								<div class="col-4">
									<input class="form-control" type="number" name="height" placeholder="Height">
								</div>
							</div>
						</div>
						<div class="tab-pane fade pt-5" id="nav-attribute" role="tabpanel" aria-labelledby="nav-attribute-tab" tabindex="0">
							<div class="row">
								<div class="col-12">
									<label class="form-label">Color</label>
									<div class="d-flex">
										<select class="form-select form-control me-4" id="color-input">
											<option >Select color</option>
											<option value="black,#000000">Black</option>
											<option value="white,#ffffff">White</option>
											<option value="red,#ff0000">Red</option>
											<option value="green,#00ff00">Green</option>
											<option value="blue,#0000ff">Blue</option>
											<option value="silver,#C0C0C0">Silver</option>
											<option value="yellow,#FFFF00">Yellow</option>
											<option value="purple,#800080">Purple</option>
											<option value="navy,#000080">Navy</option>
											<option value="brown,#A52A2A">Brown</option>
											<option value="pink,#FFC0CB">Pink</option>
											<option value="orange,#FFA500">Orange</option>
											<option value="gold,#FFD700">Gold</option>
											<option value="magenta,#FF00FF">Magenta</option>
											<option value="cyan,#00FFFF">Cyan</option>
											<option value="gray,#808080">Gray</option>
										</select>
										<button class="btn btn-gradient-primary btn-sm" type="button" id="add-color">Add</button>
									</div>
									<table class="table mt-4" id="color-table">
										<tbody></tbody>
									</table>
								</div>
							</div>
							<div class="row">
								<div class="col-12">
									<label class="form-label">Size</label>
									<div class="d-flex">
										<select class="form-select form-control me-4" id="size-input">
											<option >Select size</option>
											<option value="S">S</option>
											<option value="M">M</option>
											<option value="L">L</option>
											<option value="XL">XL</option>
											<option value="XXL">XXL</option>
										</select>
										<button class="btn btn-gradient-primary btn-sm" type="button" id="add-size">Add</button>
									</div>
									<div class=" pt-4" id="size-list"></div>
								</div>
							</div>
						</div>
						<div class="tab-pane fade" id="nav-option" role="tabpanel" aria-labelledby="nav-option-tab" tabindex="0">
							<div class="row">
								<div class="col-12">
									<label class="form-label mt-5">RAM (in GB)</label>
									<input class="form-control" type="number" name="ram" placeholder="Ram for smartphone or laptop size e.g 4GB">
								</div>
								<div class="col-12">
									<label class="form-label mt-4">ROM (storage in GB)</label>
									<input class="form-control" type="number" name="rom" placeholder="HDD size for laptop or smartphone e.g 32GB, 500GB">
								</div>
								<div class="col-12">
									<label class="form-label mt-4">Screen size (inches)</label>
									<input class="form-control" type="number" name="screen_size" placeholder="Tvs e.g 32 inch, 48 inch ">
								</div>
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
								<option value="published">Publish</option>
								<option value="draft">Draft</option>
								<option value="pending">Pending</option>
							</select>
						</div>
						<div class="col-12 pt-4">
							<div class="form-check">
							  <input class="form-check-input" type="checkbox" name="is_featured" value="1" id="isfeatured">
							  <label class="form-check-label ms-2" for="is_featured">Is featured?</label>
							</div>
						</div>
						<div class="col-12">
							<button class="btn btn-primary btn-sm mt-4"><i class="bi-ok text-white"></i> Save</button>
						</div>
					</div>
				</div>

				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Product image</h5>
					<label class="pointer image-input-label" for="image-input">
						<img height="150" class="w-100 shadow-sm rounded-3 mt-4" src="" id="image-preview">
						<input type="file" name="image" class="d-none" id="image-input">
					</label>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Product gallery</h5>
					<div class="row">
						<div class="col-12 pt-4" id="gallery-list"></div>
						<div class="col-12 pt-4">
							<label for="gallery-select">
								<span class="p-2 shadow-sm pointer"><i class="bi-plus"> Add gallery</i></span>
								<input class="d-none" type="file" name="gallery-select" id="gallery-select">
							</label>
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Categories</h5>
					<div>
						<ul class="nav flex-scolumn overflow-auto" style="height:300px; padding-left: 2px;">
							@foreach($categories as $category)
                            <li class="nav-item p-0 mt-2 border-0 w-100">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="category[]" id="check{{ $category->id }}" value="{{ $category->id }}">
                                    <label class="form-check-label ms-2" for="check{{ $category->id }}">{{ $category->name }}</label>
                                </div>
                             </li>
		                        @foreach($category->categories as $sub_category)
		                        <li class="nav-item p-0 ps-4 mt-2 border-0 w-100">
		                            <div class="form-check">
		                                <input class="form-check-input" type="checkbox" name="category[]" id="check{{ $sub_category->id }}" value="{{ $sub_category->id }}">
		                                <label class="form-check-label ms-2" for="check{{ $sub_category->id }}">{{ $sub_category->name }}</label>
		                            </div>
		                         </li>
			                        @foreach($sub_category->categories as $child_category)
			                        <li class="nav-item p-0 ps-5 mt-2 border-0 w-100">
			                            <div class="form-check">
			                                <input class="form-check-input" type="checkbox" name="category[]" id="check{{ $child_category->id }}" value="{{ $child_category->id }}">
			                                <label class="form-check-label ms-2" for="check{{ $child_category->id }}">{{ $child_category->name }}</label>
			                            </div>
			                         </li>
			                        @endforeach
		                        @endforeach
                            @endforeach
                        </ul>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Tags</h5>
					<div class="row">
						<div class="col-12">
							<div class="mt-2"  id="tag-list"></div>
						</div>
						<div class="col-12">
							<input class="form-control mt-2" type="text" name="tag-select" id="tag-select" placeholder="Enter tag then press enter">
						</div>
					</div>
				</div>
				<div class="card border-0 rounded-4 p-4 mt-4">
					<h5>Brand</h5>
					<div class="row">
						 <div class="col-12">
						 	<select class="form-select form-control mt-4" name="brand">
						 		<option value="0">Default</option>
						 		@if(count($brands))
								@foreach($brands as $brand)
								<option value="{{$brand->id}}">{{$brand->name}}</option>
								@endforeach
								@endif
						 	</select>
						 </div>
					</div>
				</div>
			</div>
		</div>
	</form>
</section>
<script type="text/javascript" src="{{ asset('/assets/vendor/tinymce/tinymce.min.js') }}"></script>
<script type="text/javascript" src="{{ asset('/assets/admin/js/product.js') }}"></script>
@endsection